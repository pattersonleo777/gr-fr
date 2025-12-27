
# GodsRods.online Fix Guide

## Problem Summary
- **HTTP (http://godsrods.online)**: Loads but 3D visualizations don't work
- **HTTPS (https://godsrods.online)**: Returns 503 Service Unavailable

---

## PART 1: Fix HTTPS/SSL (503 Error)

### Step 1: Install Certbot (Let's Encrypt)
```bash
# SSH into your Hostinger VPS
ssh root@your-vps-ip

# Update package list
apt update

# Install Certbot and Apache plugin (if using Apache)
apt install certbot python3-certbot-apache -y

# OR if using Nginx:
apt install certbot python3-certbot-nginx -y
```

### Step 2: Obtain SSL Certificate
```bash
# For Apache:
certbot --apache -d godsrods.online -d www.godsrods.online

# For Nginx:
certbot --nginx -d godsrods.online -d www.godsrods.online

# Follow the prompts:
# - Enter your email
# - Agree to terms
# - Choose whether to redirect HTTP to HTTPS (recommended: Yes)
```

### Step 3: Verify SSL Auto-Renewal
```bash
# Test renewal process
certbot renew --dry-run

# Check renewal timer status
systemctl status certbot.timer
```

### Step 4: Configure Firewall (if enabled)
```bash
# Allow HTTPS traffic
ufw allow 443/tcp
ufw status
```

---

## PART 2: Fix 3D Visualization Issues

### Issue Analysis
The 3D content doesn't render because:
1. GLTFLoader might not be loading correctly
2. Three.js version mismatch
3. Missing CORS headers for external resources
4. No default 3D models loaded

### Solution: Updated index.html

The new version I created includes:
- ✅ Correct Three.js and GLTFLoader imports
- ✅ Working placeholder 3D car model
- ✅ Proper initialization sequence
- ✅ Mouse controls (drag to rotate, scroll to zoom)
- ✅ Error handling for model loading
- ✅ Fallback placeholder when no model loaded

### Deploy the Fixed File
```bash
# Upload the new index.html to your VPS
scp /path/to/new/index.html root@your-vps:/root/gr-fr/code/vps/website/public/htdocs/

# Or if already on VPS, just replace the file
cd /root/gr-fr/code/vps/website/public/htdocs/
# Upload the new index.html file here
```

---

## PART 3: Verify Apache/Nginx Configuration

### For Apache
```bash
# Check if SSL module is enabled
a2enmod ssl
a2enmod headers

# Check your VirtualHost configuration
nano /etc/apache2/sites-available/godsrods.online.conf
```

Your config should look like:
```apache
<VirtualHost *:80>
    ServerName godsrods.online
    ServerAlias www.godsrods.online
    DocumentRoot /root/gr-fr/code/vps/website/public/htdocs
    
    <Directory /root/gr-fr/code/vps/website/public/htdocs>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Add CORS headers for 3D assets
    <IfModule mod_headers.c>
        Header set Access-Control-Allow-Origin "*"
    </IfModule>
    
    ErrorLog ${APACHE_LOG_DIR}/godsrods_error.log
    CustomLog ${APACHE_LOG_DIR}/godsrods_access.log combined
</VirtualHost>
```

```bash
# Enable the site and restart
a2ensite godsrods.online.conf
systemctl restart apache2
```

### For Nginx
```bash
# Edit Nginx config
nano /etc/nginx/sites-available/godsrods.online
```

Your config should look like:
```nginx
server {
    listen 80;
    server_name godsrods.online www.godsrods.online;
    root /root/gr-fr/code/vps/website/public/htdocs;
    index index.html;
    
    location / {
        try_files $uri $uri/ =404;
    }
    
    # Add CORS headers for 3D assets
    location ~* \.(glb|gltf|obj|mtl)$ {
        add_header Access-Control-Allow-Origin *;
    }
    
    error_log /var/log/nginx/godsrods_error.log;
    access_log /var/log/nginx/godsrods_access.log;
}
```

```bash
# Enable the site and restart
ln -s /etc/nginx/sites-available/godsrods.online /etc/nginx/sites-enabled/
systemctl restart nginx
```

---

## PART 4: Test Everything

### Test SSL
```bash
# Check SSL certificate
openssl s_client -connect godsrods.online:443 -servername godsrods.online

# Or use online tool:
# Visit: https://www.ssllabs.com/ssltest/analyze.html?d=godsrods.online
```

### Test 3D Visualization
1. Open https://godsrods.online in browser
2. Open browser console (F12)
3. Check for JavaScript errors
4. Click "Enter Rally" button
5. Verify 3D placeholder car appears
6. Test drag to rotate, scroll to zoom

---

## PART 5: Troubleshooting

### If HTTPS still shows 503:
```bash
# Check Apache/Nginx is running
systemctl status apache2
# or
systemctl status nginx

# Check error logs
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log

# Verify port 443 is listening
netstat -tlnp | grep :443
```

### If 3D still doesn't work:
```bash
# Check browser console for errors
# Common issues:
# - CORS errors → Add headers in server config
# - 404 on three.js → CDN blocked by firewall
# - Module errors → Check Three.js version compatibility
```

### Check File Permissions
```bash
# Ensure web server can read files
chown -R www-data:www-data /root/gr-fr/code/vps/website/public/htdocs
chmod -R 755 /root/gr-fr/code/vps/website/public/htdocs
```

---

## Quick Command Checklist

```bash
# 1. Install SSL
apt update && apt install certbot python3-certbot-apache -y
certbot --apache -d godsrods.online -d www.godsrods.online

# 2. Upload new index.html
# (Upload the file I provided to /root/gr-fr/code/vps/website/public/htdocs/)

# 3. Set permissions
chown -R www-data:www-data /root/gr-fr/code/vps/website/public/htdocs
chmod -R 755 /root/gr-fr/code/vps/website/public/htdocs

# 4. Restart web server
systemctl restart apache2

# 5. Test
curl -I https://godsrods.online
```

---

## Expected Results After Fix

✅ **https://godsrods.online** - Loads with valid SSL (green padlock)  
✅ **3D rotating car** - Visible in home banner  
✅ **Rally Hub workspace** - Working 3D canvas with placeholder car  
✅ **Interactive controls** - Drag to rotate, scroll to zoom  
✅ **No console errors** - Clean JavaScript execution  

---

## Next Steps (Optional Improvements)

1. **Add default 3D models**: Host .glb files on your server
2. **Optimize loading**: Add loading indicators
3. **Mobile optimization**: Touch controls for 3D
4. **CDN for assets**: Use CloudFlare for faster loading
5. **Database integration**: Store user designs
6. **Add HTTP/2**: Better performance for HTTPS

---

## Support Resources

- Let's Encrypt Docs: https://letsencrypt.org/getting-started/
- Three.js Docs: https://threejs.org/docs/
- Certbot: https://certbot.eff.org/
- SSL Test: https://www.ssllabs.com/ssltest/

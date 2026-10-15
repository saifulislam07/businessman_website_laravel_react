#!/usr/bin/env bash
# Opens port 443 (and 80, for redirects) only to Cloudflare, so nobody can bypass Cloudflare by
# hitting the server IP directly. Run as root AFTER the site works through Cloudflare, and make
# sure SSH (port 22) is already allowed in ufw. Re-run when Cloudflare changes its ranges.
set -euo pipefail

for url in https://www.cloudflare.com/ips-v4 https://www.cloudflare.com/ips-v6; do
  curl -fsS "$url" | while read -r ip; do
    [ -z "$ip" ] && continue
    ufw allow from "$ip" to any port 443 proto tcp comment 'cloudflare'
    ufw allow from "$ip" to any port 80 proto tcp comment 'cloudflare'
  done
done

# Remove any earlier blanket rules for web ports.
ufw delete allow 80/tcp 2>/dev/null || true
ufw delete allow 443/tcp 2>/dev/null || true
ufw delete allow 'Nginx Full' 2>/dev/null || true
ufw status numbered | head -40

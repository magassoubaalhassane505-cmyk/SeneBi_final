import re
import time
import urllib.parse
import urllib.request

BASE = "http://127.0.0.1:8000"
email = f"approved.e2e.{int(time.time())}@senebi.test"
password = "MotDePasseE2E99"

def get_csrf(html):
    m = re.search(r'name="_token"\s+value="([^"]+)"', html)
    return m.group(1) if m else None

def post_form(url, data, cookies):
    cookie_hdr = "; ".join(c.split(";", 1)[0] for c in cookies)
    req = urllib.request.Request(
        url,
        data=urllib.parse.urlencode(data).encode(),
        headers={"Content-Type": "application/x-www-form-urlencoded", "Cookie": cookie_hdr},
        method="POST",
    )
    class NoRedirect(urllib.request.HTTPRedirectHandler):
        def redirect_request(self, req, fp, code, msg, headers, newurl):
            return None
    opener = urllib.request.build_opener(NoRedirect)
    try:
        with opener.open(req) as resp:
            return resp.status, resp.headers.get("Location", ""), resp.read().decode("utf-8", "ignore")
    except urllib.error.HTTPError as e:
        return e.code, e.headers.get("Location", ""), e.read().decode("utf-8", "ignore")

# register
r = urllib.request.urlopen(f"{BASE}/register")
cookies = r.headers.get_all("Set-Cookie") or []
token = get_csrf(r.read().decode("utf-8", "ignore"))
post_form(f"{BASE}/register", {
    "_token": token, "name": "E2E", "email": email, "phone": "+2231",
    "company": "Co", "location": "Kayes", "password": password,
}, cookies)

open("last_e2e_email.txt", "w").write(f"{email}\n{password}")

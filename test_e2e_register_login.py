import re
import time
import urllib.parse
import urllib.request

BASE = "http://127.0.0.1:8000"
email = f"client.e2e.{int(time.time())}@senebi.test"
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
            body = resp.read().decode("utf-8", "ignore")
            return resp.status, resp.headers.get("Location", ""), body
    except urllib.error.HTTPError as e:
        body = e.read().decode("utf-8", "ignore")
        return e.code, e.headers.get("Location", ""), body

r = urllib.request.urlopen(f"{BASE}/register")
html = r.read().decode("utf-8", "ignore")
cookies = r.headers.get_all("Set-Cookie") or []
token = get_csrf(html)
status, loc, body = post_form(f"{BASE}/register", {
    "_token": token,
    "name": "Client E2E",
    "email": email,
    "phone": "+22311111111",
    "company": "Exploitation E2E",
    "location": "Kayes",
    "password": password,
}, cookies)
print("status", status, "loc", loc)
print("validation errors", "error" in body)
if "error" in body:
    import re as r2
    errs = r2.findall(r'<p>([^<]+)</p>', body[:3000])
    print("msgs", errs[:5])

open("last_e2e_email.txt", "w").write(email + "\n" + password)

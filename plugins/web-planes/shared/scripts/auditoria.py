"""Auditoría de un sitio local o de pruebas.

Revisa: robots.txt, sitemap.xml, página 404, y en cada página del sitemap
(más la portada): estado HTTP, <title>, meta description, Open Graph,
canonical, un solo <h1>, imágenes sin alt, enlaces legales en el pie y
que carguen todos los archivos propios que usa la página (img, srcset,
source, video/poster, link, script).

Uso:
    python auditoria.py http://localhost:8000 [http://localhost:8001 ...]
Sale con código 1 si encuentra problemas.
"""
import re
import sys
import urllib.error
import urllib.request
from html.parser import HTMLParser
from urllib.parse import urljoin, urlparse

LEGAL = ("privacidad", "terminos", "términos", "cookies")


def get(url):
    try:
        req = urllib.request.Request(url, headers={"User-Agent": "auditoria-web-planes"})
        with urllib.request.urlopen(req, timeout=30) as r:
            return r.status, r.read()
    except urllib.error.HTTPError as e:
        return e.code, e.read()
    except Exception as e:  # conexión rechazada, timeout…
        return 0, str(e).encode()


class Page(HTMLParser):
    def __init__(self):
        super().__init__()
        self.assets, self.meta, self.links, self.hrefs = set(), {}, {}, []
        self.title, self._in_title, self.h1, self.img_no_alt = "", False, 0, 0

    def handle_starttag(self, tag, attrs):
        a = dict(attrs)
        if tag == "title":
            self._in_title = True
        elif tag == "h1":
            self.h1 += 1
        elif tag == "meta":
            key = a.get("name") or a.get("property")
            if key:
                self.meta[key] = a.get("content", "")
        elif tag == "link":
            rel = a.get("rel", "")
            self.links.setdefault(rel, a.get("href", ""))
            if rel in ("stylesheet", "icon", "apple-touch-icon", "preload", "manifest") and a.get("href"):
                self.assets.add(a["href"])
        elif tag == "a" and a.get("href"):
            self.hrefs.append(a["href"].lower())
        if tag in ("img", "source", "video", "script", "audio") and a.get("src"):
            self.assets.add(a["src"])
        if tag == "video" and a.get("poster"):
            self.assets.add(a["poster"])
        if tag in ("img", "source") and a.get("srcset"):
            for part in a["srcset"].split(","):
                if part.strip():
                    self.assets.add(part.strip().split(" ")[0])
        if tag == "img":
            if "alt" not in a:
                self.img_no_alt += 1
            if "src" in a and not a["src"].strip():
                self.assets.add("(img con src vacío)")

    def handle_data(self, data):
        if self._in_title:
            self.title += data

    def handle_endtag(self, tag):
        if tag == "title":
            self._in_title = False


def audit(base):
    base = base.rstrip("/")
    host = urlparse(base).netloc
    problems = []

    st, robots = get(base + "/robots.txt")
    if st != 200 or b"sitemap" not in robots.lower():
        problems.append(f"robots.txt: estado {st} o sin línea Sitemap")
    st, sm = get(base + "/sitemap.xml")
    paths = [urlparse(u).path for u in re.findall(r"<loc>(.*?)</loc>", sm.decode("utf-8", "replace"))]
    if st != 200 or not paths:
        problems.append(f"sitemap.xml: estado {st}, {len(paths)} URLs")
    st, _ = get(base + "/esta-pagina-no-existe-123/")
    if st != 404:
        problems.append(f"página 404: devuelve {st}")

    assets = set()
    pages = sorted(set(paths) | {"/"})
    for path in pages:
        st, html = get(base + path)
        if st != 200:
            problems.append(f"{path}: estado {st}")
            continue
        p = Page()
        p.feed(html.decode("utf-8", "replace"))
        for need in ("description", "og:title", "og:description", "og:image"):
            if not p.meta.get(need):
                problems.append(f"{path}: falta meta {need}")
        if not p.links.get("canonical"):
            problems.append(f"{path}: falta canonical")
        if not p.title.strip():
            problems.append(f"{path}: falta <title>")
        if p.h1 != 1:
            problems.append(f"{path}: tiene {p.h1} <h1> (debe ser 1)")
        if p.img_no_alt:
            problems.append(f"{path}: {p.img_no_alt} imagen(es) sin alt")
        if not any(any(k in h for k in LEGAL) for h in p.hrefs):
            problems.append(f"{path}: no enlaza a privacidad/términos/cookies")
        if "(img con src vacío)" in p.assets:
            problems.append(f"{path}: imagen con src vacío")
            p.assets.discard("(img con src vacío)")
        og = p.meta.get("og:image", "")
        if og and urlparse(og).netloc == host:
            assets.add(urlparse(og).path)
        for a in p.assets:
            u = urlparse(urljoin(base + path, a))
            if u.netloc in (host, "") and u.scheme in ("http", "https", ""):
                assets.add(u.path)

    broken = []
    for a in sorted(assets):
        st, body = get(base + a)
        if st != 200 or not body:
            broken.append(f"archivo roto: {a} (estado {st})")

    print(f"\n### {base} · {len(pages)} páginas · {len(assets)} archivos")
    for line in problems + broken:
        print("  -", line)
    if not problems and not broken:
        print("  OK, sin problemas")
    return not (problems or broken)


if __name__ == "__main__":
    if hasattr(sys.stdout, "reconfigure"):
        sys.stdout.reconfigure(encoding="utf-8")
    if len(sys.argv) < 2:
        print(__doc__)
        sys.exit(2)
    ok = all([audit(u) for u in sys.argv[1:]])
    sys.exit(0 if ok else 1)

#!/usr/bin/env python3
"""
Выгружает все открытые записи D&D 5e SRD 2014 из dnd5eapi.co.

Создаёт:
- monsters.json
- spells.json
- magic_items.json
- artifacts.json
- conditions_api.json
- languages_api.json
- skills_api.json
- proficiencies_api.json
- weapon_properties_api.json

Запуск:
    python download_srd.py

Требуется Python 3.10+ и интернет.
"""
from __future__ import annotations
import json
import time
import urllib.request
from pathlib import Path
from typing import Any

BASE = "https://www.dnd5eapi.co/api/2014"
OUT = Path(__file__).resolve().parent / "downloaded"
OUT.mkdir(exist_ok=True)

ENDPOINTS = {
    "monsters": "monsters.json",
    "spells": "spells.json",
    "magic-items": "magic_items.json",
    "conditions": "conditions_api.json",
    "languages": "languages_api.json",
    "skills": "skills_api.json",
    "proficiencies": "proficiencies_api.json",
    "weapon-properties": "weapon_properties_api.json",
}

def get_json(url: str, retries: int = 4) -> Any:
    headers = {"User-Agent": "dnd5e-srd-json-downloader/1.0"}
    req = urllib.request.Request(url, headers=headers)
    last_error = None
    for attempt in range(retries):
        try:
            with urllib.request.urlopen(req, timeout=30) as r:
                return json.loads(r.read().decode("utf-8"))
        except Exception as exc:
            last_error = exc
            time.sleep(1.5 * (attempt + 1))
    raise RuntimeError(f"Не удалось загрузить {url}: {last_error}")

def download_collection(endpoint: str) -> list[dict]:
    index = get_json(f"{BASE}/{endpoint}")
    results = index.get("results", [])
    full = []
    total = len(results)
    for n, item in enumerate(results, 1):
        url = item.get("url") or f"/api/2014/{endpoint}/{item['index']}"
        full.append(get_json("https://www.dnd5eapi.co" + url))
        print(f"{endpoint}: {n}/{total}", end="\r")
        time.sleep(0.03)
    print(f"{endpoint}: {total}/{total}")
    return full

def main() -> None:
    collections = {}
    for endpoint, filename in ENDPOINTS.items():
        data = download_collection(endpoint)
        collections[endpoint] = data
        (OUT / filename).write_text(
            json.dumps(data, ensure_ascii=False, indent=2),
            encoding="utf-8"
        )

    magic_items = collections["magic-items"]
    artifacts = [
        item for item in magic_items
        if str(item.get("rarity", {}).get("name", "")).lower() == "artifact"
        or "artifact" in str(item.get("rarity", "")).lower()
    ]
    (OUT / "artifacts.json").write_text(
        json.dumps(artifacts, ensure_ascii=False, indent=2),
        encoding="utf-8"
    )

    manifest = {
        "ruleset": "D&D 5e SRD 2014",
        "source_api": BASE,
        "files": {
            filename: len(collections[endpoint])
            for endpoint, filename in ENDPOINTS.items()
        },
        "artifacts_count": len(artifacts),
    }
    (OUT / "manifest.json").write_text(
        json.dumps(manifest, ensure_ascii=False, indent=2),
        encoding="utf-8"
    )
    print(f"\nГотово. Файлы находятся в: {OUT}")

if __name__ == "__main__":
    main()

# D&D 5e SRD Extended JSON

Готовые русские справочники:
- conditions_ru.json
- skills_ru.json
- languages_ru.json
- tools_ru.json
- weapon_properties_ru.json
- armor_properties_ru.json

Для получения полных файлов монстров, заклинаний и магических предметов SRD:
1. Установите Python 3.
2. Откройте терминал в этой папке.
3. Выполните:
   python download_srd.py
4. Результат появится в папке downloaded/.

Скрипт создаёт monsters.json, spells.json, magic_items.json, artifacts.json
и дополнительные справочники непосредственно из D&D 5e SRD API.

Важно: это только открытый SRD 5.1, а не весь контент всех официальных книг.

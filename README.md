# CPA Product Landing Management System

To'liq PHP + MySQL asosidagi CPA mahsulot landing generatori. Bir domen asosida cheksiz mahsulotlar uchun landing sahifalar yaratish, lidlarni yig'ish, ularga Facebook Pixel qo'shish va oqim (flow) havolalariga POST orqali yuborish imkonini beradi.

- **Admin panel (Bootstrap 5)**: xavfsiz login, boshqaruv paneli, mahsulot CRUD, nusxalash, global sozlamalar.
- **Landing generator**: `https://domain.com/product/{slug}` ko'rinishidagi tezkor, SEO-ready va O'zbek tilidagi sahifalar.
- **Lead boshqaruvi**: lid jadvali, sana/mahsulot filtr, retry statlari, CSV eksport.
- **Facebook Pixel**: PageView, ViewContent, Lead eventlari avtomatik yuboriladi.
- **Flow integratsiyasi**: har bir lead flow havolasiga backend orqali POST qilinadi va loglarga yoziladi.
- **Kategoriya va qidiruv**: bosh sahifada yashil tema, chap menyu, qidiruv, “eng ko’p sotilganlar”.
- **API to'plami**: lead qabul qilish, flow retry, loglarni ko’rish va health-check endpointlari (token + rate limit bilan himoyalangan).

## Texnologiyalar
- PHP 8+, PDO (MySQL)
- Bootstrap 5 (admin), custom CSS (landing)
- Vanilla JS (AJAX form, input mask)
- Apache + `.htaccess` (shared hosting bilan mos)

## Loyihaning tuzilishi
```
app/
  bootstrap.php          # Avtoulash, sessiya, DB
  controllers/           # MVC controllerlar
  controllers/Api/       # Tokenli REST endpointlar
  core/                  # Router, Controller, Validator, Database
  helpers/helpers.php    # global helperlar
  helpers/security.php   # sanitizatsiya, token, rate-limit, login guard
  models/                # Admin, Product, Lead, Category, Setting
    FlowLog.php          # Flow log modeli
  services/              # FileUploader, LeadDispatcher
    LeadManager.php      # Lead yaratish + dispatch servici
  views/                 # Admin, landing, home, layoutlar
config/
  env.php                # Yopiq konfiguratsiya (DB, app)
public/
  index.php              # Front controller
  install.php            # O'rnatish skripti
  assets/                # Front CSS/JS
  admin/assets/          # Admin CSS/JS
  uploads/               # Mahsulot rasmlari
routes/web.php           # URL marshrutlar
sql/schema.sql           # DB sxemasi va dastlabki ma'lumotlar
storage/
  uploads/               # Media fayllar
  cache/                 # Rate-limit va login guard fayllari
```

## O'rnatish bosqichlari
1. **Fayllarni serverga joylang** (rootga). Apache docroot iloji bo'lsa `public/` bo'lsin, aks holda repo ildizidagi `.htaccess` barcha so'rovlarni `public/`ga yo'naltiradi.
2. **`config/env.php` faylini sozlang** (yoki `env.example.php` dan nusxa oling) va MySQL ma'lumotlarini kiriting.
3. **MySQLda bo'sh baza yarating** (nomi `config/env.php` dagi `database`).
4. Brauzerda `https://domain.com/install.php` oching: jadval va boshlang'ich adminni yaratish formasi paydo bo'ladi.
   - Formani to'ldirib `Jadvallarni yaratish` tugmasini bosing.
   - Muvaffaqiyatli tugagach, `install.php` faylini o'chirib tashlash tavsiya etiladi.
5. `config/env.php` ichida `api.key` qiymatini **kuchli token** bilan to'ldiring (API endpointlar uchun majburiy).
6. `https://domain.com/admin/login` ga o'tib, o'rnatish paytida kiritilgan admin ma'lumotlari bilan tizimga kiring.
7. Sozlamalardan Facebook Pixel ID ni kiriting (ixtiyoriy).
8. Mahsulot qo'shing: nom, tavsif, narx, kategoriya, flow URL, video (ixtiyoriy) va rasm.
9. Landing sahifani `https://domain.com/product/{slug}` orqali ko'ring, lead formasi yoki `POST /api/lead` endpointini test qiling.

### Yangilanish (mavjud bazalar uchun)
- `sql/schema.sql` ichidagi `leads` jadvaliga `retry_count` INT va `flow_status ENUM('pending','sent','failed')` bo'limlarini qo'shing.
- `flow_logs` jadvalini yarating.
- Tayyor skript: `sql/migrations/2025_01_flow_logs_retry_count.sql`.
- `config/env.php` fayliga `api` bo'limi qo'shib, API kalitini belgilang.
- `storage/cache` papkasini yozishga ruxsat bering (rate-limit va login guard uchun).

## Xavfsizlik
- CSRF tokenlar sessiya bo'yicha rotatsiya qilinadi, AJAX javoblari yangilangan tokenni qaytaradi.
- Parollar `password_hash` (BCRYPT) bilan saqlanadi.
- PDO prepared statements SQL injeksiyalarni bloklaydi.
- Rasm yuklashda MIME/size tekshirish, faqat JPG/PNG/WebP va 3MB gacha.
- HTML chiqishlari `htmlspecialchars` orqali XSS'dan himoyalangan.
- API endpointlari `Authorization: Bearer {API_KEY}` yoki `X-API-Key` orqali token bilan himoyalangan.
- `storage/cache` da IP + token asosida rate-limit saqlanadi (default 60 r/min).
- Admin login bruteforce himoyasi: 5 xato urinish → 15 daqiqa blok.

## Lead oqimi
1. Landing formasi AJAX orqali `/product/{slug}/lead` endpointiga ma'lumot yuboradi.
2. Server lidni bazaga *pending* statusida yozadi va `App\Services\LeadDispatcher` yordamida `flow_url` manziliga POST qiladi.
3. Har bir POST urinish `flow_logs` jadvaliga saqlanadi.
4. Javobga qarab `flow_status` sent/failed ga o'tadi, retry_count yangilanadi.
5. Foydalanuvchi shu sahifada qoladi va "Buyurtmangiz qabul qilindi" xabari chiqadi, token qayta generatsiya qilinadi.
6. Facebook Pixel `Lead` eventi avtomatik jo'natiladi (agar ID mavjud bo'lsa).

## Eksport / filter
- `Admin > Lidlar` bo'limida mahsulot va sana oralig'i bo'yicha filterlash mumkin.
- `CSV eksport` tugmasi shu filter natijasini UTF-8 CSV ko'rinishida yuklab beradi.

## REST API lar
Barcha so'rovlar `Authorization: Bearer {API_KEY}` (yoki `X-API-Key: {API_KEY}`) bilan autentifikatsiyalanadi.

| Endpoint | Method | Tavsif | Qo'shimcha |
| --- | --- | --- | --- |
| `/api/lead` | POST | Front-end integratsiyasi uchun lead yaratadi va flow'ga yuboradi | Body: `full_name`, `phone`, `product_slug` yoki `product_id`. CORS: `Access-Control-Allow-Origin: *` |
| `/api/retry-leads` | POST | `flow_status='failed'` leadlarni yana flow'ga yuboradi | Cron uchun. Javobda processed/sent/failed statistikasi |
| `/api/flow-logs` | GET | `flow_logs` jadvalidagi yozuvlarni chiqaradi | Query: `lead_id`, `status` (HTTP code) |
| `/api/health` | GET | Health-check (`status`, `db`, `version`) | Monitor va uptime robotlari uchun |

### CORS (Lead API)
```
POST /api/lead
Headers:
  Authorization: Bearer YOUR_API_KEY
  Content-Type: application/json
Body:
{
  "full_name": "Ali Valiyev",
  "phone": "+998901112233",
  "product_slug": "super-detox"
}
```
Javob: `{ "success": true, "lead_id": 123, "status": "sent", "message": "OK" }`

### Flow Retry Cron
```
*/5 * * * * curl -X POST https://domain.com/api/retry-leads \
  -H "Authorization: Bearer YOUR_API_KEY"
```

## Deployment bo'yicha eslatmalar
- PHP 8.1+ tavsiya etiladi, `curl` moduli mavjud bo'lsa yaxshi, bo'lmasa stream fallback ishlaydi.
- `public/uploads/` katalogi yozish huquqiga ega bo'lishi kerak.
- `storage/cache/` katalogi ham yoziladigan bo'lsin (rate-limit, login guard).
- Apache'da `mod_rewrite` yoqilgan bo'lishi kerak.
- O'rnatishdan so'ng `public/install.php` ni o'chirish xavfsizlikni oshiradi.
- Cron bilan `/api/retry-leads` endpointini chaqirish leadlar oqimini uzluksiz qiladi.

## Keyingi qadamlar (ixtiyoriy)
- Rolga asoslangan ko'p admin qo'llab-quvvatlash
- Kategoriya CRUD'i
- Webhook loglari / flow javoblarini saqlash
- Redis/session store bilan kengaytirilgan seans boshqaruvi
- Flow loglar uchun admin UI
- Webhook retry rejalashtirish parametrlarini UI'dan boshqarish

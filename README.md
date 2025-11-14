# CPA Product Landing Management System

To'liq PHP + MySQL asosidagi CPA mahsulot landing generatori. Bir domen asosida cheksiz mahsulotlar uchun landing sahifalar yaratish, lidlarni yig'ish, ularga Facebook Pixel qo'shish va oqim (flow) havolalariga POST orqali yuborish imkonini beradi.

## Asosiy imkoniyatlar
- **Admin panel (Bootstrap 5)**: xavfsiz login, boshqaruv paneli, mahsulot CRUD, mahsulot nusxalash, sozlamalar.
- **Landing generator**: har bir mahsulot uchun `https://domain.com/product/{slug}` ko'rinishidagi sahifa, tezkor va SEO uchun optimallashtirilgan, O'zbek tilidagi interfeys.
- **Lead boshqaruvi**: lidlarni jadvalda ko'rish, mahsulot va sana bo'yicha filterlash, CSV eksport.
- **Facebook Pixel**: admin kiritgan global Pixel ID barcha landing sahifalarga qo'shiladi (`PageView`, `ViewContent`, `Lead`).
- **Flow integratsiyasi**: foydalanuvchi sahifadan chiqmagan holda lid backend orqali mahsulotga biriktirilgan flow URL'ga POST qilinadi.
- **Kategoriya va qidiruv**: bosh sahifada menyu (yuqori chap) orqali toifalar, eng ko'p sotilgan va yangi mahsulotlar, nomi bo'yicha qidiruv.

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
  core/                  # Router, Controller, Validator, Database
  helpers/helpers.php    # global helperlar
  models/                # Admin, Product, Lead, Category, Setting
  services/              # FileUploader, LeadDispatcher
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
```

## O'rnatish bosqichlari
1. **Fayllarni serverga joylang** (rootga). Apache docroot iloji bo'lsa `public/` bo'lsin, aks holda repo ildizidagi `.htaccess` barcha so'rovlarni `public/`ga yo'naltiradi.
2. **`config/env.php` faylini sozlang** (yoki `env.example.php` dan nusxa oling) va MySQL ma'lumotlarini kiriting.
3. **MySQLda bo'sh baza yarating** (nomi `config/env.php` dagi `database`).
4. Brauzerda `https://domain.com/install.php` oching: jadval va boshlang'ich adminni yaratish formasi paydo bo'ladi.
   - Formani to'ldirib `Jadvallarni yaratish` tugmasini bosing.
   - Muvaffaqiyatli tugagach, `install.php` faylini o'chirib tashlash tavsiya etiladi.
5. `https://domain.com/admin/login` ga o'tib, o'rnatish paytida kiritilgan admin ma'lumotlari bilan tizimga kiring.
6. Sozlamalardan Facebook Pixel ID ni kiriting (ixtiyoriy).
7. Mahsulot qo'shing: nom, tavsif, narx, kategoriya, flow URL, video (ixtiyoriy) va rasm.
8. Landing sahifani `https://domain.com/product/{slug}` orqali ko'ring, lead formasi test qiling.

## Xavfsizlik
- CSRF token barcha POST formalarida ishlatiladi.
- Parollar `password_hash` (BCRYPT) bilan saqlanadi.
- PDO prepared statements SQL injeksiyalarni bloklaydi.
- Rasm yuklashda MIME/size tekshirish, faqat JPG/PNG/WebP va 3MB gacha.
- HTML chiqishlari `htmlspecialchars` orqali XSS'dan himoyalangan.

## Lead oqimi
1. Landing formasi AJAX orqali `/product/{slug}/lead` endpointiga ma'lumot yuboradi.
2. Server lidni bazaga yozadi va `App\Services\LeadDispatcher` yordamida `flow_url` manziliga POST qiladi.
3. Foydalanuvchi shu sahifada qoladi va "Buyurtmangiz qabul qilindi" xabari chiqadi.
4. Facebook Pixel `Lead` eventi avtomatik jo'natiladi (agar ID mavjud bo'lsa).

## Eksport / filter
- `Admin > Lidlar` bo'limida mahsulot va sana oralig'i bo'yicha filterlash mumkin.
- `CSV eksport` tugmasi shu filter natijasini UTF-8 CSV ko'rinishida yuklab beradi.

## Deployment bo'yicha eslatmalar
- PHP 8.1+ tavsiya etiladi, `curl` moduli mavjud bo'lsa yaxshi, bo'lmasa stream fallback ishlaydi.
- `public/uploads/` katalogi yozish huquqiga ega bo'lishi kerak.
- Apache'da `mod_rewrite` yoqilgan bo'lishi kerak.
- O'rnatishdan so'ng `public/install.php` ni o'chirish xavfsizlikni oshiradi.

## Keyingi qadamlar (ixtiyoriy)
- Rolga asoslangan ko'p admin qo'llab-quvvatlash
- Kategoriya CRUD'i
- Webhook loglari / flow javoblarini saqlash
- Redis/session store bilan kengaytirilgan seans boshqaruvi

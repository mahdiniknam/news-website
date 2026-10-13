# ترمیم اسلاگ‌های قدیمی که داخلشان `/` دارد

> بعد از `git pull` روی هاست، یکی از دو روش زیر را اجرا کنید.
> بدون این ترمیم هم لینک قدیمی با فیکس `HomeController` باز می‌شود، ولی ترمیم بهتر است.

## روش ۱ — با `php artisan tinker` (کپی‌پیست مستقیم)

```bash
php artisan tinker
```

سپس داخل `tinker` این خط را **کامل کپی و Enter بزنید**:

```php
DB::table('posts')->where('slug','like','%/%')->get(['id','slug'])->each(fn($r)=>DB::table('posts')->where('id',$r->id)->update(['slug'=>str_replace('/','-',trim($r->slug,'-'))])); echo 'fixed: '.DB::table('posts')->where('slug','like','%/%')->count().' remaining with slash';
```

خروجی باید `fixed: 0 remaining with slash` باشد.

```php
exit
```

---

## روش ۲ — یک خطی بدون ورود به tinker

```bash
php artisan tinker --execute="DB::table('posts')->where('slug','like','%/%')->get(['id','slug'])->each(fn(\$r)=>DB::table('posts')->where('id',\$r->id)->update(['slug'=>str_replace('/','-',trim(\$r->slug,'-'))])); echo 'remaining with slash: '.DB::table('posts')->where('slug','like','%/%')->count().PHP_EOL;"
```

---

## روش ۳ — برای دسته‌بندی و تگ هم (اگر لازم شد)

```bash
php artisan tinker --execute="DB::table('categories')->where('slug','like','%/%')->get(['id','slug'])->each(fn(\$r)=>DB::table('categories')->where('id',\$r->id)->update(['slug'=>str_replace('/','-',trim(\$r->slug,'-'))])); DB::table('tags')->where('slug','like','%/%')->get(['id','slug'])->each(fn(\$r)=>DB::table('tags')->where('id',\$r->id)->update(['slug'=>str_replace('/','-',trim(\$r->slug,'-'))])); echo 'done'.PHP_EOL;"
```

---

## بعدش حتما کش را پاک کنید

```bash
php artisan config:clear && php artisan view:clear && php artisan route:clear
```

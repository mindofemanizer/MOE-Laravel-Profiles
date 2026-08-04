# MOE-Laravel-Profiles

Profil key-value per entitas (polymorphic): tambahkan data profil fleksibel ke model apa pun tanpa mengubah skema tabelnya.

## Instalasi

```bash
composer require moe/laravel-profiles:dev-main-main
```

Publish config lalu migrasi:

```bash
php artisan vendor:publish --provider="Moe\Profiles\MoeProfilesServiceProvider" --tag=moe-profiles-config
php artisan migrate
```

## Yang Termasuk

| Komponen | Keterangan |
|---|---|
| `Profile` (model) | Penyimpanan key-value dengan relasi morph `profileable` |
| `HasProfiles` (trait) | Tambahkan ke model agar memiliki profil |
| Facade `Profile` | API baca/tulis profil |
| Services | Logika bisnis profil |

## Penggunaan

```php
use Moe\Profiles\Traits\HasProfiles;

class Customer extends Model
{
    use HasProfiles;
}

$customer->setProfile('phone', '0812xxxx');
$customer->getProfile('phone');
```

Atau via facade:

```php
use Moe\Profiles\Facades\Profile;

Profile::for($customer)->get('phone');
```

## Dependensi

- PHP `^8.2`, Illuminate `^11.0 | ^12.0 | ^13.0`

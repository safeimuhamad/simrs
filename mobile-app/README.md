# SIM Rumah Sakit mobile app Flutter

Project mobile terpisah untuk Android dan iOS, terintegrasi dengan web SIM Rumah Sakit melalui endpoint `/api/mobile`.

## Jalankan lokal

1. Pastikan web SIM Rumah Sakit aktif di `http://localhost/sim-rs`.
2. Import migration `database/2026_05_27_mobile_api_tokens.sql` ke database web.
3. Install dependency mobile:

```bash
flutter pub get
flutter run
```

Untuk Android emulator, ganti `apiBaseUrl` di `lib/api_client.dart` menjadi:

```dart
const apiBaseUrl = 'http://10.0.2.2/sim-rs/api/mobile';
```

Untuk device fisik, gunakan IP laptop dalam jaringan Wi-Fi yang sama.

## Scope MVP

- Registrasi member dengan email verification existing.
- Login member/client.
- Forgot password.
- List event berbayar publik.
- Pembelian tiket member.
- Tiket saya, detail tiket, upload bukti bayar.
- Konten event: agenda, speaker, materi, galeri, Q&A, polling, sertifikat, informasi.
- Dashboard client, event client, peserta & check-in, timeline, dokumen, approval, notifikasi.
- Scan QR tiket oleh petugas/client.

import 'dart:io';

import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import 'package:shared_preferences/shared_preferences.dart';

import 'api_client.dart';

void main() {
  runApp(const IventloApp());
}

const _purple = Color(0xFF2B0F4F);
const _magenta = Color(0xFFA12CB2);
const _pink = Color(0xFFC43A76);
const _soft = Color(0xFFF6F4FB);

class IventloApp extends StatefulWidget {
  const IventloApp({super.key});

  @override
  State<IventloApp> createState() => _IventloAppState();
}

class _IventloAppState extends State<IventloApp> {
  String? token;
  Map<String, dynamic>? user;
  String screen = 'login';
  Map<String, dynamic>? selectedEvent;
  Map<String, dynamic>? selectedOrder;
  String? contentType;
  bool booting = true;

  @override
  void initState() {
    super.initState();
    _restore();
  }

  Future<void> _restore() async {
    final prefs = await SharedPreferences.getInstance();
    final savedToken = prefs.getString('token');
    final savedRole = prefs.getString('role');
    final savedName = prefs.getString('name');
    final savedEmail = prefs.getString('email');
    setState(() {
      token = savedToken;
      if (savedToken != null) {
        user = {'name': savedName ?? 'Iventlo user', 'email': savedEmail ?? '', 'role': savedRole ?? 'member'};
        screen = savedRole == 'client' ? 'clientHome' : 'memberHome';
      }
      booting = false;
    });
  }

  Future<void> _saveSession(String nextToken, Map<String, dynamic> nextUser, String home) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('token', nextToken);
    await prefs.setString('name', (nextUser['name'] ?? '').toString());
    await prefs.setString('email', (nextUser['email'] ?? '').toString());
    await prefs.setString('role', (nextUser['role'] ?? '').toString());
    setState(() {
      token = nextToken;
      user = nextUser;
      screen = home == 'client' ? 'clientHome' : 'memberHome';
    });
  }

  Future<void> _logout() async {
    if (token != null) {
      ApiClient(token: token).post('/auth/logout', {}).catchError((_) => <String, dynamic>{});
    }
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
    setState(() {
      token = null;
      user = null;
      selectedEvent = null;
      selectedOrder = null;
      screen = 'login';
    });
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'Iventlo',
      theme: ThemeData(
        scaffoldBackgroundColor: _soft,
        colorScheme: ColorScheme.fromSeed(seedColor: _magenta),
        fontFamily: 'Roboto',
        useMaterial3: true,
      ),
      home: booting
          ? const Center(child: CircularProgressIndicator())
          : _route(),
    );
  }

  Widget _route() {
    return switch (screen) {
      'register' => RegisterScreen(goLogin: () => setState(() => screen = 'login'), goVerify: () => setState(() => screen = 'verify')),
      'verify' => VerifyScreen(goLogin: () => setState(() => screen = 'login')),
      'forgot' => ForgotScreen(goLogin: () => setState(() => screen = 'login')),
      'memberHome' => MemberHome(
          token: token!,
          user: user!,
          logout: _logout,
          openEvents: () => setState(() => screen = 'publicEvents'),
          openOrder: (order) => setState(() {
            selectedOrder = order;
            screen = 'orderDetail';
          }),
          openContent: (order, type) => setState(() {
            selectedOrder = order;
            contentType = type;
            screen = 'contentList';
          }),
        ),
      'publicEvents' => PublicEvents(
          token: token,
          back: () => setState(() => screen = 'memberHome'),
          open: (event) => setState(() {
            selectedEvent = event;
            screen = 'publicEventDetail';
          }),
        ),
      'publicEventDetail' => PublicEventDetail(
          token: token!,
          event: selectedEvent!,
          back: () => setState(() => screen = 'publicEvents'),
          onPurchased: (order) => setState(() {
            selectedOrder = order;
            screen = 'orderDetail';
          }),
        ),
      'orderDetail' => OrderDetail(
          token: token!,
          order: selectedOrder!,
          back: () => setState(() => screen = 'memberHome'),
          openContent: (type) => setState(() {
            contentType = type;
            screen = 'contentList';
          }),
        ),
      'contentList' => ContentList(
        token: token!,
        order: user?['role'] == 'member' ? selectedOrder : null,
        event: selectedEvent,
        type: contentType,
          back: () => setState(() => screen = user?['role'] == 'client' ? 'clientEventDetail' : 'orderDetail'),
        ),
      'clientHome' => ClientHome(
          token: token!,
          user: user!,
          logout: _logout,
          openEvent: (event) => setState(() {
            selectedEvent = event;
            screen = 'clientEventDetail';
          }),
        ),
      'clientEventDetail' => ClientEventDetail(
          event: selectedEvent!,
          back: () => setState(() => screen = 'clientHome'),
          openMenu: (type) => setState(() {
            contentType = type;
            screen = switch (type) {
              'attendees' => 'clientAttendees',
              'timeline' => 'clientTimeline',
              'documents' => 'clientDocuments',
              'approvals' => 'clientApprovals',
              'summary' => 'clientEventDetail',
              _ => 'contentList',
            };
          }),
        ),
      'clientAttendees' => ClientAttendees(token: token!, event: selectedEvent!, back: () => setState(() => screen = 'clientEventDetail')),
      'clientTimeline' => SimpleEndpoint(token: token!, title: 'Timeline', path: '/client/events/${selectedEvent!['id']}/timeline', dataKey: 'timeline', back: () => setState(() => screen = 'clientEventDetail')),
      'clientDocuments' => SimpleEndpoint(token: token!, title: 'Dokumen', path: '/client/events/${selectedEvent!['id']}/documents', dataKey: 'documents', back: () => setState(() => screen = 'clientEventDetail')),
      'clientApprovals' => SimpleEndpoint(token: token!, title: 'Approval', path: '/client/events/${selectedEvent!['id']}/approvals', dataKey: 'approvals', back: () => setState(() => screen = 'clientEventDetail')),
      _ => LoginScreen(
          onLogin: _saveSession,
          goRegister: () => setState(() => screen = 'register'),
          goForgot: () => setState(() => screen = 'forgot'),
        ),
    };
  }
}

class LoginScreen extends StatefulWidget {
  const LoginScreen({required this.onLogin, required this.goRegister, required this.goForgot, super.key});

  final Future<void> Function(String token, Map<String, dynamic> user, String home) onLogin;
  final VoidCallback goRegister;
  final VoidCallback goForgot;

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final email = TextEditingController();
  final password = TextEditingController();
  bool busy = false;

  Future<void> submit() async {
    setState(() => busy = true);
    try {
      final payload = await ApiClient().post('/auth/login', {
        'email': email.text,
        'password': password.text,
        'device_name': 'Iventlo Flutter',
      });
      await widget.onLogin(payload['token'].toString(), payload['user'] as Map<String, dynamic>, payload['home'].toString());
    } catch (error) {
      showError(context, 'Login gagal', error);
    } finally {
      setState(() => busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      child: ListView(
        padding: const EdgeInsets.all(18),
        children: [
          Container(
            constraints: const BoxConstraints(minHeight: 300),
            padding: const EdgeInsets.all(26),
            decoration: BoxDecoration(color: _purple, borderRadius: BorderRadius.circular(26)),
            child: const Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.end,
              children: [
                Text('IVENTLO', style: TextStyle(color: Colors.white, fontSize: 28, fontWeight: FontWeight.w900, letterSpacing: 5)),
                SizedBox(height: 48),
                Text('Selamat datang di event Anda', style: TextStyle(color: Colors.white, fontSize: 30, fontWeight: FontWeight.w900)),
                SizedBox(height: 12),
                Text('Akses tiket digital, agenda, materi, sertifikat, dan informasi event dalam satu aplikasi.', style: TextStyle(color: Color(0xFFF3D9FF), height: 1.6)),
              ],
            ),
          ),
          const SizedBox(height: 16),
          CardBox(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const PageTitle('Masuk ke akun Anda'),
                const Muted('Gunakan akun member atau client portal.'),
                Field(label: 'Email', controller: email, keyboardType: TextInputType.emailAddress),
                Field(label: 'Password', controller: password, obscureText: true),
                PrimaryButton(label: busy ? 'Memproses...' : 'Masuk', onPressed: busy ? null : submit),
                TextButton(onPressed: widget.goForgot, child: const Text('Lupa password?')),
                Center(child: TextButton(onPressed: widget.goRegister, child: const Text('Belum punya akun? Daftar sekarang'))),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class RegisterScreen extends StatefulWidget {
  const RegisterScreen({required this.goLogin, required this.goVerify, super.key});

  final VoidCallback goLogin;
  final VoidCallback goVerify;

  @override
  State<RegisterScreen> createState() => _RegisterScreenState();
}

class _RegisterScreenState extends State<RegisterScreen> {
  final email = TextEditingController();

  Future<void> submit() async {
    try {
      await ApiClient().post('/auth/register/request', {'email': email.text});
      if (!mounted) return;
      showMessage(context, 'Link verifikasi sudah dikirim ke email Anda.');
      widget.goVerify();
    } catch (error) {
      showError(context, 'Registrasi gagal', error);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AuthPage(title: 'Daftar member Iventlo', subtitle: 'Masukkan email untuk menerima link verifikasi.', back: widget.goLogin, children: [
      Field(label: 'Email', controller: email, keyboardType: TextInputType.emailAddress),
      PrimaryButton(label: 'Kirim link verifikasi', onPressed: submit),
    ]);
  }
}

class VerifyScreen extends StatefulWidget {
  const VerifyScreen({required this.goLogin, super.key});

  final VoidCallback goLogin;

  @override
  State<VerifyScreen> createState() => _VerifyScreenState();
}

class _VerifyScreenState extends State<VerifyScreen> {
  final token = TextEditingController();
  final name = TextEditingController();
  final birthDate = TextEditingController();
  final gender = TextEditingController(text: 'male');
  final password = TextEditingController();
  final confirmation = TextEditingController();

  Future<void> submit() async {
    try {
      await ApiClient().post('/auth/register/complete', {
        'token': token.text,
        'name': name.text,
        'birth_date': birthDate.text,
        'gender': gender.text,
        'password': password.text,
        'password_confirmation': confirmation.text,
      });
      if (!mounted) return;
      showMessage(context, 'Akun berhasil diaktifkan. Silakan masuk.');
      widget.goLogin();
    } catch (error) {
      showError(context, 'Verifikasi gagal', error);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AuthPage(title: 'Lengkapi profil', subtitle: 'Isi data diri setelah membuka link verifikasi email.', back: widget.goLogin, children: [
      Field(label: 'Token verifikasi', controller: token),
      Field(label: 'Nama lengkap', controller: name),
      Field(label: 'Tanggal lahir (YYYY-MM-DD)', controller: birthDate),
      Field(label: 'Jenis kelamin (male/female)', controller: gender),
      Field(label: 'Password', controller: password, obscureText: true),
      Field(label: 'Konfirmasi password', controller: confirmation, obscureText: true),
      PrimaryButton(label: 'Aktifkan akun', onPressed: submit),
    ]);
  }
}

class ForgotScreen extends StatefulWidget {
  const ForgotScreen({required this.goLogin, super.key});

  final VoidCallback goLogin;

  @override
  State<ForgotScreen> createState() => _ForgotScreenState();
}

class _ForgotScreenState extends State<ForgotScreen> {
  final email = TextEditingController();

  Future<void> submit() async {
    try {
      await ApiClient().post('/auth/forgot-password', {'email': email.text});
      if (!mounted) return;
      showMessage(context, 'Jika email terdaftar, link reset password akan dikirimkan.');
      widget.goLogin();
    } catch (error) {
      showError(context, 'Gagal', error);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AuthPage(title: 'Lupa password', subtitle: 'Kami kirimkan link reset ke email Anda.', back: widget.goLogin, children: [
      Field(label: 'Email', controller: email, keyboardType: TextInputType.emailAddress),
      PrimaryButton(label: 'Kirim link reset', onPressed: submit),
    ]);
  }
}

class MemberHome extends StatefulWidget {
  const MemberHome({required this.token, required this.user, required this.logout, required this.openEvents, required this.openOrder, required this.openContent, super.key});

  final String token;
  final Map<String, dynamic> user;
  final VoidCallback logout;
  final VoidCallback openEvents;
  final void Function(Map<String, dynamic> order) openOrder;
  final void Function(Map<String, dynamic> order, String type) openContent;

  @override
  State<MemberHome> createState() => _MemberHomeState();
}

class _MemberHomeState extends State<MemberHome> {
  Map<String, dynamic> data = {'orders': []};

  @override
  void initState() {
    super.initState();
    load();
  }

  Future<void> load() async {
    data = await ApiClient(token: widget.token).get('/member/dashboard');
    if (mounted) setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    final orders = List<Map<String, dynamic>>.from(data['orders'] ?? []);
    final latest = orders.isEmpty ? null : orders.first;

    return PortalPage(user: widget.user, logout: widget.logout, child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        PageTitle('Selamat datang, ${widget.user['name']}'),
        const Muted('Pilih event berbayar, kelola tiket, dan akses menu peserta.'),
        EventCard(title: 'Lihat semua event berbayar', subtitle: 'Pilih event dan beli tiket langsung dari aplikasi.', onTap: widget.openEvents),
        if (latest != null) EventCard(
          title: latest['event_title'].toString(),
          subtitle: '${latest['event_date']} · ${latest['venue']}\nStatus pembayaran: ${latest['payment_status']}',
          onTap: () => widget.openOrder(latest),
        ),
        const SectionTitle('Menu utama'),
        MenuGrid(items: memberMenus, onTap: (type) {
          if (latest == null) {
            showMessage(context, 'Beli tiket event terlebih dahulu.');
            return;
          }
          widget.openContent(latest, type);
        }),
      ],
    ));
  }
}

class PublicEvents extends StatefulWidget {
  const PublicEvents({required this.token, required this.back, required this.open, super.key});

  final String? token;
  final VoidCallback back;
  final void Function(Map<String, dynamic> event) open;

  @override
  State<PublicEvents> createState() => _PublicEventsState();
}

class _PublicEventsState extends State<PublicEvents> {
  List<Map<String, dynamic>> events = [];

  @override
  void initState() {
    super.initState();
    ApiClient(token: widget.token).get('/events').then((payload) {
      setState(() => events = List<Map<String, dynamic>>.from(payload['events'] ?? []));
    }).catchError((error) => showError(context, 'Gagal memuat event', error));
  }

  @override
  Widget build(BuildContext context) {
    return Page(title: 'Event berbayar', back: widget.back, child: Column(
      children: events.map((event) => EventCard(
        title: event['title'].toString(),
        subtitle: '${event['event_date']} · ${event['venue']}\n${money(event['ticket_price'])} · kuota ${event['participant_quota']}',
        imageUrl: event['cover_image_url']?.toString(),
        onTap: () => widget.open(event),
      )).toList(),
    ));
  }
}

class PublicEventDetail extends StatefulWidget {
  const PublicEventDetail({required this.token, required this.event, required this.back, required this.onPurchased, super.key});

  final String token;
  final Map<String, dynamic> event;
  final VoidCallback back;
  final void Function(Map<String, dynamic> order) onPurchased;

  @override
  State<PublicEventDetail> createState() => _PublicEventDetailState();
}

class _PublicEventDetailState extends State<PublicEventDetail> {
  final phone = TextEditingController();
  final quantity = TextEditingController(text: '1');

  Future<void> buy() async {
    try {
      final payload = await ApiClient(token: widget.token).post('/events/${widget.event['slug']}/purchase', {
        'buyer_phone': phone.text,
        'quantity': quantity.text,
      });
      if (!mounted) return;
      showMessage(context, 'Pesanan tiket berhasil dibuat.');
      widget.onPurchased(payload['order'] as Map<String, dynamic>);
    } catch (error) {
      showError(context, 'Gagal beli tiket', error);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Page(title: widget.event['title'].toString(), back: widget.back, child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (widget.event['cover_image_url'] != null) ClipRRect(borderRadius: BorderRadius.circular(18), child: Image.network(widget.event['cover_image_url'].toString(), height: 220, width: double.infinity, fit: BoxFit.cover)),
        Muted('${widget.event['event_date']} · ${widget.event['venue']}'),
        Text(money(widget.event['ticket_price']), style: const TextStyle(color: _pink, fontSize: 24, fontWeight: FontWeight.w900)),
        Text((widget.event['description'] ?? '').toString()),
        Field(label: 'Nomor WhatsApp', controller: phone, keyboardType: TextInputType.phone),
        Field(label: 'Jumlah tiket', controller: quantity, keyboardType: TextInputType.number),
        PrimaryButton(label: 'Beli tiket', onPressed: buy),
      ],
    ));
  }
}

class OrderDetail extends StatefulWidget {
  const OrderDetail({required this.token, required this.order, required this.back, required this.openContent, super.key});

  final String token;
  final Map<String, dynamic> order;
  final VoidCallback back;
  final void Function(String type) openContent;

  @override
  State<OrderDetail> createState() => _OrderDetailState();
}

class _OrderDetailState extends State<OrderDetail> {
  Map<String, dynamic> detail = {};

  @override
  void initState() {
    super.initState();
    detail = widget.order;
    load();
  }

  Future<void> load() async {
    final payload = await ApiClient(token: widget.token).get('/member/orders/${widget.order['order_number']}');
    setState(() => detail = payload['order'] as Map<String, dynamic>);
  }

  Future<void> uploadProof() async {
    final picked = await ImagePicker().pickImage(source: ImageSource.gallery, imageQuality: 85);
    if (picked == null) return;
    try {
      await ApiClient(token: widget.token).upload('/member/orders/${detail['order_number']}/payment-proof', File(picked.path), 'payment_proof');
      if (!mounted) return;
      showMessage(context, 'Bukti bayar berhasil dikirim untuk verifikasi.');
    } catch (error) {
      showError(context, 'Upload gagal', error);
    }
  }

  @override
  Widget build(BuildContext context) {
    final tickets = List<Map<String, dynamic>>.from(detail['tickets'] ?? []);
    return Page(title: 'Detail tiket', back: widget.back, child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SectionTitle(detail['event_title']?.toString() ?? 'Event'),
        Muted('${detail['order_number']} · ${detail['payment_status']}'),
        Text('Total: ${money(detail['total_amount'])}'),
        PrimaryButton(label: 'Upload bukti bayar', onPressed: uploadProof),
        const SectionTitle('Tiket'),
        for (final ticket in tickets) CardBox(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(ticket['attendee_name'].toString(), style: cardTitleStyle),
          Text(ticket['ticket_code'].toString()),
          Muted('Status hadir: ${ticket['check_in_status']}'),
        ])),
        MenuGrid(items: memberMenus, onTap: widget.openContent),
      ],
    ));
  }
}

class ContentList extends StatefulWidget {
  const ContentList({required this.token, required this.type, required this.back, this.order, this.event, super.key});

  final String token;
  final Map<String, dynamic>? order;
  final Map<String, dynamic>? event;
  final String? type;
  final VoidCallback back;

  @override
  State<ContentList> createState() => _ContentListState();
}

class _ContentListState extends State<ContentList> {
  List<Map<String, dynamic>> contents = [];

  @override
  void initState() {
    super.initState();
    final path = widget.order != null ? '/member/orders/${widget.order!['order_number']}/contents' : '/client/events/${widget.event!['id']}/contents';
    ApiClient(token: widget.token).get(path).then((payload) {
      final all = List<Map<String, dynamic>>.from(payload['contents'] ?? []);
      setState(() => contents = all.where((item) => widget.type == null || item['content_type'] == widget.type).toList());
    }).catchError((error) => showError(context, 'Gagal memuat konten', error));
  }

  @override
  Widget build(BuildContext context) {
    return Page(title: labelForType(widget.type), back: widget.back, child: Column(
      children: contents.isEmpty
          ? [const Muted('Belum ada konten untuk menu ini.')]
          : contents.map((item) => CardBox(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text(item['title'].toString(), style: cardTitleStyle),
                if (item['subtitle'] != null) Text(item['subtitle'].toString()),
                if (item['scheduled_at'] != null) Muted(item['scheduled_at'].toString()),
                if (item['location'] != null) Muted(item['location'].toString()),
                if (item['description'] != null) Text(item['description'].toString()),
              ]))).toList(),
    ));
  }
}

class ClientHome extends StatefulWidget {
  const ClientHome({required this.token, required this.user, required this.logout, required this.openEvent, super.key});

  final String token;
  final Map<String, dynamic> user;
  final VoidCallback logout;
  final void Function(Map<String, dynamic> event) openEvent;

  @override
  State<ClientHome> createState() => _ClientHomeState();
}

class _ClientHomeState extends State<ClientHome> {
  Map<String, dynamic> data = {'events': [], 'summary': {}};

  @override
  void initState() {
    super.initState();
    ApiClient(token: widget.token).get('/client/dashboard').then((payload) => setState(() => data = payload)).catchError((error) => showError(context, 'Gagal memuat dashboard', error));
  }

  @override
  Widget build(BuildContext context) {
    final summary = Map<String, dynamic>.from(data['summary'] ?? {});
    final events = List<Map<String, dynamic>>.from(data['events'] ?? []);
    return PortalPage(user: widget.user, logout: widget.logout, child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const PageTitle('Dashboard client'),
        const Muted('Pantau event, milestone, dokumen, approval, dan kehadiran peserta.'),
        Row(children: [
          Expanded(child: StatBox(label: 'Event aktif', value: summary['events'] ?? 0)),
          const SizedBox(width: 10),
          Expanded(child: StatBox(label: 'Approval', value: summary['pending_approvals'] ?? 0)),
        ]),
        const SectionTitle('Event saya'),
        for (final event in events) EventCard(
          title: event['title'].toString(),
          subtitle: '${event['event_date']} · ${event['status']} · progres ${event['progress'] ?? 0}%\nTerjual ${event['sold_tickets'] ?? 0} · hadir ${event['attended_count'] ?? 0}',
          onTap: () => widget.openEvent(event),
        ),
      ],
    ));
  }
}

class ClientEventDetail extends StatelessWidget {
  const ClientEventDetail({required this.event, required this.back, required this.openMenu, super.key});

  final Map<String, dynamic> event;
  final VoidCallback back;
  final void Function(String type) openMenu;

  @override
  Widget build(BuildContext context) {
    return Page(title: event['title'].toString(), back: back, child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Muted('${event['event_date']} · ${event['venue']}'),
        Row(children: [
          Expanded(child: StatBox(label: 'Kuota', value: event['participant_quota'] ?? 0)),
          const SizedBox(width: 10),
          Expanded(child: StatBox(label: 'Terjual', value: event['sold_tickets'] ?? 0)),
          const SizedBox(width: 10),
          Expanded(child: StatBox(label: 'Hadir', value: event['attended_count'] ?? 0)),
        ]),
        MenuGrid(items: clientMenus, onTap: openMenu),
      ],
    ));
  }
}

class ClientAttendees extends StatefulWidget {
  const ClientAttendees({required this.token, required this.event, required this.back, super.key});

  final String token;
  final Map<String, dynamic> event;
  final VoidCallback back;

  @override
  State<ClientAttendees> createState() => _ClientAttendeesState();
}

class _ClientAttendeesState extends State<ClientAttendees> {
  List<Map<String, dynamic>> attendees = [];
  bool scanning = false;

  @override
  void initState() {
    super.initState();
    load();
  }

  Future<void> load() async {
    final payload = await ApiClient(token: widget.token).get('/client/events/${widget.event['id']}/attendees');
    setState(() => attendees = List<Map<String, dynamic>>.from(payload['attendees'] ?? []));
  }

  Future<void> checkIn(String code) async {
    try {
      await ApiClient(token: widget.token).post('/staff/check-in/$code', {});
      if (!mounted) return;
      showMessage(context, 'Peserta berhasil di-check-in.');
      setState(() => scanning = false);
      load();
    } catch (error) {
      showError(context, 'Check-in gagal', error);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (scanning) {
      return QrScanner(title: 'Scan QR tiket peserta', back: () => setState(() => scanning = false), onCode: checkIn);
    }

    return Page(title: 'Peserta & check-in', back: widget.back, child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        PrimaryButton(label: 'Scan QR tiket peserta', onPressed: () => setState(() => scanning = true)),
        for (final attendee in attendees) CardBox(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(attendee['attendee_name'].toString(), style: cardTitleStyle),
          Text('${attendee['ticket_code']} · ${attendee['payment_status']}'),
          Muted('Status hadir: ${attendee['check_in_status']}'),
        ])),
      ],
    ));
  }
}

class SimpleEndpoint extends StatefulWidget {
  const SimpleEndpoint({required this.token, required this.title, required this.path, required this.dataKey, required this.back, super.key});

  final String token;
  final String title;
  final String path;
  final String dataKey;
  final VoidCallback back;

  @override
  State<SimpleEndpoint> createState() => _SimpleEndpointState();
}

class _SimpleEndpointState extends State<SimpleEndpoint> {
  List<Map<String, dynamic>> items = [];

  @override
  void initState() {
    super.initState();
    ApiClient(token: widget.token).get(widget.path).then((payload) {
      setState(() => items = List<Map<String, dynamic>>.from(payload[widget.dataKey] ?? []));
    }).catchError((error) => showError(context, 'Gagal memuat data', error));
  }

  @override
  Widget build(BuildContext context) {
    return Page(title: widget.title, back: widget.back, child: Column(
      children: items.isEmpty
          ? [const Muted('Belum ada data.')]
          : items.map((item) => CardBox(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Text((item['title'] ?? item['reference_no'] ?? item['file_name'] ?? '-').toString(), style: cardTitleStyle),
                Muted((item['description'] ?? item['category'] ?? item['status'] ?? item['due_date'] ?? '').toString()),
              ]))).toList(),
    ));
  }
}

class QrScanner extends StatefulWidget {
  const QrScanner({required this.title, required this.back, required this.onCode, super.key});

  final String title;
  final VoidCallback back;
  final Future<void> Function(String code) onCode;

  @override
  State<QrScanner> createState() => _QrScannerState();
}

class _QrScannerState extends State<QrScanner> {
  bool locked = false;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      body: Stack(
        children: [
          MobileScanner(onDetect: (capture) async {
            if (locked || capture.barcodes.isEmpty) return;
            final code = capture.barcodes.first.rawValue;
            if (code == null) return;
            locked = true;
            await widget.onCode(code);
            await Future<void>.delayed(const Duration(milliseconds: 900));
            locked = false;
          }),
          SafeArea(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(widget.title, style: const TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w900)),
                  const Spacer(),
                  PrimaryButton(label: 'Kembali', onPressed: widget.back),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class AppScaffold extends StatelessWidget {
  const AppScaffold({required this.child, super.key});

  final Widget child;

  @override
  Widget build(BuildContext context) => Scaffold(body: SafeArea(child: child));
}

class PortalPage extends StatelessWidget {
  const PortalPage({required this.user, required this.logout, required this.child, super.key});

  final Map<String, dynamic> user;
  final VoidCallback logout;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return AppScaffold(
      child: ListView(
        padding: EdgeInsets.zero,
        children: [
          Container(
            padding: const EdgeInsets.all(24),
            decoration: const BoxDecoration(color: _purple),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Text('IVENTLO', style: TextStyle(color: Colors.white, fontSize: 22, fontWeight: FontWeight.w900, letterSpacing: 4)),
              const SizedBox(height: 10),
              Text(user['name'].toString(), style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w700)),
              Text(user['role'].toString(), style: const TextStyle(color: Color(0xFFEADFFF))),
            ]),
          ),
          Padding(padding: const EdgeInsets.all(20), child: child),
          Padding(padding: const EdgeInsets.all(20), child: PrimaryButton(label: 'Keluar', onPressed: logout)),
        ],
      ),
    );
  }
}

class Page extends StatelessWidget {
  const Page({required this.title, required this.back, required this.child, super.key});

  final String title;
  final VoidCallback back;
  final Widget child;

  @override
  Widget build(BuildContext context) => AppScaffold(
    child: ListView(
      padding: const EdgeInsets.all(20),
      children: [
        Align(alignment: Alignment.centerLeft, child: TextButton(onPressed: back, child: const Text('← Kembali'))),
        PageTitle(title),
        const SizedBox(height: 12),
        child,
      ],
    ),
  );
}

class AuthPage extends StatelessWidget {
  const AuthPage({required this.title, required this.subtitle, required this.back, required this.children, super.key});

  final String title;
  final String subtitle;
  final VoidCallback back;
  final List<Widget> children;

  @override
  Widget build(BuildContext context) => AppScaffold(
    child: ListView(
      padding: const EdgeInsets.all(18),
      children: [
        CardBox(child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            TextButton(onPressed: back, child: const Text('← Kembali')),
            PageTitle(title),
            Muted(subtitle),
            ...children,
          ],
        )),
      ],
    ),
  );
}

class CardBox extends StatelessWidget {
  const CardBox({required this.child, super.key});

  final Widget child;

  @override
  Widget build(BuildContext context) => Container(
    width: double.infinity,
    margin: const EdgeInsets.only(bottom: 12),
    padding: const EdgeInsets.all(18),
    decoration: BoxDecoration(
      color: Colors.white,
      borderRadius: BorderRadius.circular(22),
      boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 16, offset: const Offset(0, 8))],
    ),
    child: child,
  );
}

class Field extends StatelessWidget {
  const Field({required this.label, required this.controller, this.keyboardType, this.obscureText = false, super.key});

  final String label;
  final TextEditingController controller;
  final TextInputType? keyboardType;
  final bool obscureText;

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.only(top: 14),
    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(label, style: const TextStyle(fontWeight: FontWeight.w700)),
      const SizedBox(height: 8),
      TextField(
        controller: controller,
        keyboardType: keyboardType,
        obscureText: obscureText,
        decoration: InputDecoration(
          hintText: label,
          filled: true,
          fillColor: Colors.white,
          border: OutlineInputBorder(borderRadius: BorderRadius.circular(14), borderSide: const BorderSide(color: Color(0xFFDCE1EC))),
        ),
      ),
    ]),
  );
}

class PrimaryButton extends StatelessWidget {
  const PrimaryButton({required this.label, required this.onPressed, super.key});

  final String label;
  final VoidCallback? onPressed;

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.only(top: 12),
    child: FilledButton(
      style: FilledButton.styleFrom(backgroundColor: _magenta, padding: const EdgeInsets.symmetric(vertical: 16), shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14))),
      onPressed: onPressed,
      child: Text(label, style: const TextStyle(fontWeight: FontWeight.w900)),
    ),
  );
}

class EventCard extends StatelessWidget {
  const EventCard({required this.title, required this.subtitle, required this.onTap, this.imageUrl, super.key});

  final String title;
  final String subtitle;
  final String? imageUrl;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) => GestureDetector(
    onTap: onTap,
    child: CardBox(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      if (imageUrl != null) ClipRRect(borderRadius: BorderRadius.circular(14), child: Image.network(imageUrl!, height: 150, width: double.infinity, fit: BoxFit.cover)),
      Text(title, style: cardTitleStyle),
      Muted(subtitle),
    ])),
  );
}

class MenuGrid extends StatelessWidget {
  const MenuGrid({required this.items, required this.onTap, super.key});

  final List<List<String>> items;
  final void Function(String type) onTap;

  @override
  Widget build(BuildContext context) => Wrap(
    spacing: 12,
    runSpacing: 12,
    children: items.map((item) => GestureDetector(
      onTap: () => onTap(item[0]),
      child: Container(
        width: (MediaQuery.of(context).size.width - 56) / 2,
        constraints: const BoxConstraints(minHeight: 132),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(18)),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          const Text('◆', style: TextStyle(color: _magenta, fontSize: 24)),
          Text(item[1], style: cardTitleStyle),
          Muted(item[2]),
        ]),
      ),
    )).toList(),
  );
}

class StatBox extends StatelessWidget {
  const StatBox({required this.label, required this.value, super.key});

  final String label;
  final dynamic value;

  @override
  Widget build(BuildContext context) => CardBox(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
    Text(value.toString(), style: const TextStyle(color: _purple, fontSize: 24, fontWeight: FontWeight.w900)),
    Muted(label),
  ]));
}

class PageTitle extends StatelessWidget {
  const PageTitle(this.text, {super.key});

  final String text;

  @override
  Widget build(BuildContext context) => Text(text, style: const TextStyle(color: Color(0xFF141B34), fontSize: 28, fontWeight: FontWeight.w900));
}

class SectionTitle extends StatelessWidget {
  const SectionTitle(this.text, {super.key});

  final String text;

  @override
  Widget build(BuildContext context) => Padding(
    padding: const EdgeInsets.only(top: 10, bottom: 10),
    child: Text(text, style: const TextStyle(color: Color(0xFF141B34), fontSize: 20, fontWeight: FontWeight.w900)),
  );
}

class Muted extends StatelessWidget {
  const Muted(this.text, {super.key});

  final String text;

  @override
  Widget build(BuildContext context) => Text(text, style: const TextStyle(color: Color(0xFF7C849B), height: 1.5));
}

final cardTitleStyle = const TextStyle(color: Color(0xFF141B34), fontSize: 16, fontWeight: FontWeight.w900);

final memberMenus = [
  ['tickets', 'Tiket saya', 'Lihat tiket dan QR Code untuk check-in'],
  ['agenda', 'Agenda acara', 'Lihat jadwal lengkap acara'],
  ['speaker', 'Daftar speaker', 'Kenali para pembicara'],
  ['material', 'Materi', 'Unduh materi dan dokumen'],
  ['certificate', 'Sertifikat', 'Unduh sertifikat setelah event'],
  ['information', 'Informasi venue', 'Lokasi, peta, dan info penting'],
  ['gallery', 'Galeri', 'Lihat dokumentasi event'],
  ['qna', 'Q&A session', 'Ajukan pertanyaan ke pembicara'],
  ['polling', 'Polling & voting', 'Ikut polling dan voting live'],
];

final clientMenus = [
  ['summary', 'Ringkasan event', 'Status, kuota, progres, dan venue'],
  ['attendees', 'Peserta & check-in', 'Lihat peserta daftar dan hadir'],
  ['timeline', 'Timeline', 'Milestone yang terlihat untuk client'],
  ['documents', 'Dokumen', 'Proposal, kontrak, invoice, report'],
  ['approvals', 'Approval', 'Lihat dan proses approval'],
  ['agenda', 'Agenda', 'Kelola agenda acara'],
  ['speaker', 'Pembicara', 'Kelola profil pembicara'],
  ['material', 'Materi', 'Upload materi event'],
  ['gallery', 'Galeri', 'Upload dokumentasi acara'],
  ['qna', 'Q&A', 'Kelola pertanyaan'],
  ['polling', 'Polling', 'Kelola polling event'],
  ['certificate', 'Sertifikat', 'Kelola sertifikat'],
  ['information', 'Informasi', 'Info venue dan catatan peserta'],
];

String money(dynamic value) => NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0).format(num.tryParse(value.toString()) ?? 0);

String labelForType(String? type) {
  return {
        'tickets': 'Tiket saya',
        'agenda': 'Agenda acara',
        'speaker': 'Daftar speaker',
        'material': 'Materi',
        'certificate': 'Sertifikat',
        'information': 'Informasi',
        'gallery': 'Galeri',
        'qna': 'Q&A session',
        'polling': 'Polling & voting',
      }[type] ??
      'Konten event';
}

void showMessage(BuildContext context, String message) {
  ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(message)));
}

void showError(BuildContext context, String title, Object error) {
  showDialog<void>(
    context: context,
    builder: (context) => AlertDialog(
      title: Text(title),
      content: Text(error.toString()),
      actions: [TextButton(onPressed: () => Navigator.pop(context), child: const Text('Tutup'))],
    ),
  );
}

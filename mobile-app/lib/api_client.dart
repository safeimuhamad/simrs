import 'dart:convert';
import 'dart:io';

import 'package:http/http.dart' as http;

const apiBaseUrl = 'http://localhost/sim-rs/api/mobile';

class ApiException implements Exception {
  ApiException(this.message);

  final String message;

  @override
  String toString() => message;
}

class ApiClient {
  ApiClient({this.token});

  final String? token;

  Map<String, String> get _headers => {
        'Accept': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      };

  Future<Map<String, dynamic>> get(String path) async {
    final response = await http.get(Uri.parse('$apiBaseUrl$path'), headers: _headers);
    return _decode(response);
  }

  Future<Map<String, dynamic>> post(String path, Map<String, dynamic> body) async {
    final response = await http.post(
      Uri.parse('$apiBaseUrl$path'),
      headers: {..._headers, 'Content-Type': 'application/json'},
      body: jsonEncode(body),
    );
    return _decode(response);
  }

  Future<Map<String, dynamic>> upload(String path, File file, String fieldName) async {
    final request = http.MultipartRequest('POST', Uri.parse('$apiBaseUrl$path'));
    request.headers.addAll(_headers);
    request.files.add(await http.MultipartFile.fromPath(fieldName, file.path));
    final streamed = await request.send();
    return _decode(await http.Response.fromStream(streamed));
  }

  Map<String, dynamic> _decode(http.Response response) {
    final decoded = jsonDecode(response.body.isEmpty ? '{}' : response.body) as Map<String, dynamic>;
    if (response.statusCode >= 400 || decoded['success'] == false) {
      throw ApiException((decoded['message'] ?? 'Request gagal diproses.').toString());
    }
    return decoded;
  }
}

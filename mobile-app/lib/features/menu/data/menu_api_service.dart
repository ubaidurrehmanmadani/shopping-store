import 'dart:convert';

import 'package:http/http.dart' as http;

import '../../../core/config/app_config.dart';
import 'models/category_summary.dart';
import 'models/product_summary.dart';

class MenuApiService {
  final http.Client _client;

  MenuApiService({http.Client? client}) : _client = client ?? http.Client();

  Future<List<CategorySummary>> fetchCategories() async {
    final response =
        await _client.get(Uri.parse('${AppConfig.apiBaseUrl}/categories'));

    if (response.statusCode != 200) {
      throw Exception('Failed to load categories');
    }

    final body = jsonDecode(response.body);
    final items = _extractList(body);

    return items
        .map((item) => CategorySummary.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  Future<List<ProductSummary>> fetchProducts() async {
    final response =
        await _client.get(Uri.parse('${AppConfig.apiBaseUrl}/products'));

    if (response.statusCode != 200) {
      throw Exception('Failed to load products');
    }

    final body = jsonDecode(response.body);
    final items = _extractList(body);

    return items
        .map((item) => ProductSummary.fromJson(item as Map<String, dynamic>))
        .toList();
  }

  List<dynamic> _extractList(dynamic body) {
    if (body is List<dynamic>) {
      return body;
    }

    if (body is Map<String, dynamic>) {
      final data = body['data'];

      if (data is List<dynamic>) {
        return data;
      }
    }

    return const [];
  }
}

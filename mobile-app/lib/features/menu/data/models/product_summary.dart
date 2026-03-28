class ProductSummary {
  const ProductSummary({
    required this.id,
    required this.name,
    required this.slug,
    required this.shortDescription,
    required this.price,
    required this.salePrice,
    required this.imageUrl,
    required this.currency,
  });

  final int id;
  final String name;
  final String slug;
  final String shortDescription;
  final double price;
  final double? salePrice;
  final String imageUrl;
  final String currency;

  double get currentPrice => salePrice ?? price;

  factory ProductSummary.fromJson(Map<String, dynamic> json) {
    return ProductSummary(
      id: json['id'] as int,
      name: (json['name'] ?? '') as String,
      slug: (json['slug'] ?? '') as String,
      shortDescription: (json['short_description'] ?? '') as String,
      price: double.tryParse('${json['price']}') ?? 0,
      salePrice: json['sale_price'] == null
          ? null
          : double.tryParse('${json['sale_price']}'),
      imageUrl: (json['image_url'] ?? json['image_source'] ?? '') as String,
      currency: (json['currency'] ?? 'USD') as String,
    );
  }
}

class CategorySummary {
  const CategorySummary({
    required this.id,
    required this.name,
    required this.slug,
    required this.description,
    required this.imageUrl,
  });

  final int id;
  final String name;
  final String slug;
  final String description;
  final String imageUrl;

  factory CategorySummary.fromJson(Map<String, dynamic> json) {
    return CategorySummary(
      id: json['id'] as int,
      name: (json['name'] ?? '') as String,
      slug: (json['slug'] ?? '') as String,
      description: (json['description'] ?? '') as String,
      imageUrl: (json['image_url'] ?? json['image_source'] ?? '') as String,
    );
  }
}

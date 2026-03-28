import 'package:flutter/material.dart';

import '../core/theme/app_theme.dart';
import 'routes/app_router.dart';

class RushBiteApp extends StatelessWidget {
  const RushBiteApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'RushBite',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light(),
      home: AppRouter.home(),
    );
  }
}

import 'package:flutter/material.dart';

import '../../shared/presentation/main_navigation_shell.dart';

class AppRouter {
  const AppRouter._();

  static Widget home() {
    return const MainNavigationShell();
  }
}

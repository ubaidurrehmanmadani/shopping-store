# RushBite Mobile App

This folder contains the starter Flutter customer app for the deployed Laravel backend.

Current API base URL:

`https://shopping-store-main-wyjfa6.free.laravel.cloud/api/v1`

## What is included

- Feature-based Flutter structure
- App shell with bottom navigation
- API configuration
- Basic theme
- Home screen starter
- Cart/profile placeholders
- Menu API service for categories and products

## Folder structure

```text
mobile-app/
  lib/
    app/
      app.dart
      routes/
    core/
      config/
      theme/
    features/
      auth/
      cart/
      home/
      menu/
      profile/
    shared/
      presentation/
      widgets/
```

## Important

Flutter is not installed in the current environment, so the native project folders were not generated here.

After installing Flutter on your Mac, run this inside `mobile-app`:

```bash
flutter create . --platforms=android,ios
flutter pub get
flutter run
```

That command will generate:

- `android/`
- `ios/`
- platform build files

and keep the Dart files already added in `lib/`.

## Recommended learning order

1. Install Flutter and Xcode/Android Studio requirements
2. Run `flutter doctor`
3. Generate the native folders with `flutter create . --platforms=android,ios`
4. Run the starter app
5. Build the login screen
6. Connect auth
7. Build menu, cart, checkout, and orders

## First commands to run on your Mac

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/shop-backend/mobile-app
flutter doctor
flutter create . --platforms=android,ios
flutter pub get
flutter run
```

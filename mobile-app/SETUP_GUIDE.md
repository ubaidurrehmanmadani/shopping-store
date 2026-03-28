# Flutter Setup Guide For This Project

This project now has a starter Flutter customer app in `mobile-app/`.

## Will your MacBook Pro 2019 work?

Yes, a 2019 MacBook Pro is generally fine for Flutter development.

You can build:

- Android apps
- iPhone apps
- iOS Simulator apps

What you need:

- Xcode installed
- Android Studio installed
- Flutter SDK installed

## Step 1: Install Flutter

Pick one of these:

### Option A: Homebrew

```bash
brew install --cask flutter
```

### Option B: Manual download

- Download Flutter SDK from `flutter.dev`
- Extract it somewhere stable, for example:
  - `/Users/apple/development/flutter`
- Add it to your shell path

Example `~/.zshrc` line:

```bash
export PATH="$PATH:/Users/apple/development/flutter/bin"
```

Then reload shell:

```bash
source ~/.zshrc
```

## Step 2: Install Xcode

- Install Xcode from the App Store
- Open it once
- Accept the license

Then run:

```bash
sudo xcode-select --switch /Applications/Xcode.app/Contents/Developer
sudo xcodebuild -runFirstLaunch
```

## Step 3: Install Android Studio

Install Android Studio and inside it install:

- Android SDK
- Android SDK Platform
- Android Emulator
- Android SDK Command-line Tools

## Step 4: Verify tooling

Run:

```bash
flutter doctor
```

Keep fixing issues until the main checks are green.

## Step 5: Generate mobile native folders

Go to the starter app:

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/shop-backend/mobile-app
flutter create . --platforms=android,ios
flutter pub get
```

## Step 6: Run the app

For iPhone simulator:

```bash
flutter run
```

For Android emulator:

```bash
flutter devices
flutter run -d <device-id>
```

## Current API URL

The app is already configured for:

`https://shopping-store-main-wyjfa6.free.laravel.cloud/api/v1`

You can change it later in:

- `lib/core/config/app_config.dart`

## What we should build next

1. Login and register
2. Token storage
3. Product listing
4. Product detail
5. Cart
6. Checkout
7. Orders
8. Profile and addresses

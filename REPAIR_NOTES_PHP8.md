# Inventory project repair notes

Branch: `repair/php8-inventory`

This branch contains an initial repair pass. It is intentionally separate from `master`; review and test before merging.

## Changes made

- Fixed the missing semicolon in `admin.php`.
- Replaced deprecated `strftime()` usage in `make_date()` with `date()`.
- Corrected the random-string upper bound (previously could index past the final character).
- Corrected `remove_junk()`: PHP 8 requires `strip_tags()`'s allowed-tags argument to be a string, not `ENT_QUOTES`; HTML escaping now uses `htmlspecialchars()` with UTF-8 and substitution.
- Improved `validate_fields()` to handle missing POST keys safely.
- Fixed `display_msg()` so it accumulates all flash messages rather than overwriting each one.
- Added transaction helpers to the mysqli wrapper.
- Updated sale creation to validate product/quantity/date and deduct stock transactionally, preventing overselling.
- Updated sale editing to restore old quantity and apply the new quantity in one transaction.
- Updated sale deletion to restore inventory in the same transaction.
- Regenerate the PHP session ID on successful login.
- Correct access-level enforcement and add the missing driver lookup helper.
- Prevent self-deletion and deleting products with sales history.
- Harden image uploads (content/MIME/size checks and random filenames).
- Hide SQL statements from client-facing database errors.
- Introduce `password_hash()` / `password_verify()` with automatic SHA-1-to-modern-hash upgrade on successful legacy login; update password creation/change paths.
- Add a GitHub Actions PHP 8.2 syntax-lint workflow for root, includes, and layouts.

## Important remaining work

- This is not a complete audit or a release-ready build. Run PHP lint and integration tests locally.
- Migrate SHA-1 user passwords safely to `password_hash()` / `password_verify()`.
- Replace raw SQL string interpolation with prepared statements.
- Add CSRF tokens and convert destructive GET actions to POST.
- Review upload MIME/content validation and generated filenames.
- Fix other broken pages and authorization gaps (signup/customer/delivery/order/truck/driver paths).
- Preserve sale unit price and cost snapshots for accurate historical reporting.
- Review and test the separate legacy `stock/` CodeIgniter application.
- Configure non-root database credentials and disable SQL error disclosure in production.

## Test checklist

1. Import the included database into a disposable local database.
2. Run `php -l` over all application PHP files.
3. Login and verify the dashboard.
4. Create a sale with sufficient stock; verify stock decreases exactly once.
5. Attempt a sale exceeding stock; verify rejection and unchanged stock.
6. Edit a sale quantity up and down; verify stock reconciliation.
7. Delete a sale; verify stock restoration.
8. Force a failed operation and verify transaction rollback.
9. Test reports, product CRUD, user CRUD, uploads, and each delivery/order route.
10. Test with the intended PHP and MySQL versions before production deployment.

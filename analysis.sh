#phpstan
echo "Running phpstan..."
vendor/bin/phpstan analyse --memory-limit=2048M
#phpcs
echo "Fixing automatically phpcs errors..."
vendor/bin/phpcbf -s -p -d memory_limit=2048M
echo "Running phpcs..."
vendor/bin/phpcs -s -p -d memory_limit=2048M
#phpmd
echo "Running phpmd..."
vendor/bin/phpmd app ansi ./phpmd.xml
vendor/bin/phpmd database/seeders ansi ./phpmd.xml
vendor/bin/phpmd src ansi ./phpmd.xml

# Seed default VAT tax rates for existing installations

INSERT INTO ip_tax_rates (tax_rate_name, tax_rate_percent)
SELECT 'VAT19', 19.00
WHERE NOT EXISTS (
    SELECT 1 FROM ip_tax_rates WHERE tax_rate_name = 'VAT19' AND tax_rate_percent = 19.00
);

INSERT INTO ip_tax_rates (tax_rate_name, tax_rate_percent)
SELECT 'VAT5', 5.00
WHERE NOT EXISTS (
    SELECT 1 FROM ip_tax_rates WHERE tax_rate_name = 'VAT5' AND tax_rate_percent = 5.00
);

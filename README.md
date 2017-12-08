# Add to Cart via CSV
Add to Cart via CSV is a B2B extension which helps the Wholesaler to add the products to cart from CSV file without having to go through an existing complex process

# Features 
1) There is a seperate section called "Add to Cart via CSV" in the Customer dashboard
2) Customer/Wholesaler can upload the CSV file(csv file should contain two columns `sku` and `qty`).
3) After successfull upload, it is automatically redirects to Cart Page.
4) Interactive Success/Error messages.
5) Customer/Wholesaler can see the previous transactions.
6) Customer/Wholesaler can repeat the previously done transactions.

# How to Install

1) Clone the latest from repository
2) Extract files in the Magento root directory in the folder app/code/Evry/Uploadorder
3) php bin/magento setup:upgrade
4) php bin/magento setup:di:compile
5) php bin/magento cache:clean

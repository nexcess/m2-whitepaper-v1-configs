#1450714848
ls
#1450714849
cd public_html/
#1450714853
 wget  https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.0+Samples.zip
#1450715195
unzip Magento-CE-2.0.0+Samples.zip 
#1450715276
php -v
#1450715294
ls
#1450715296
cd ..
#1450715297
ls
#1450715299
cd var/
#1450715300
ls
#1450715324
exit
#1450715371
cd public_html/
#1450715372
ls
#1450715396
find . -type f -exec chmod 644 {} \;
#1450715426
find . -type d -exec chmod 755 {} \;
#1450715439
sed -i 's/0770/0775/g' vendor/magento/framework/Filesystem/DriverInterface.php
#1450715445
sed -i 's/0660/0664/g' vendor/magento/framework/Filesystem/DriverInterface.php
#1450715456
exit
#1450715555
cd public_html/
#1450715560
chmod 755 -R var/
#1450715572
chmod 775 -R var/
#1450715572
ls
#1450715575
ll
#1450715583
chmod 777 -R var/
#1450715652
find . -type d -exec chmod 2775 {} \;
#1450715778
cd var/
#1450715818
cd ..
#1450715822
find . -type d -exec chmod 2775 {} \;
#1450715860
exit
#1450716470
ls
#1450716474
mysql -u -p
#1450716507
mysql -uphpseven_mage2 -p
#1450716836
php -v
#1450717020
ls
#1450717024
cd public_html/
#1450717024
ls
#1450717089
php magento setup:install --base-url=http://http://php7.magento2-demo.nexcess.net --db-host=localhost --db-name=phpseven_magento2 --db-user=phpseven_mage2 --db-password=Mbalparda89jcr --admin-firstname=Magento --admin-lastname=User --admin-email=user@example.com --admin-user=admin --admin-password=admin123 --language=en_US --currency=USD --timezone=America/Chicago --use-rewrites=1
#1450717094
cd bin
#1450717097
php magento setup:install --base-url=http://http://php7.magento2-demo.nexcess.net --db-host=localhost --db-name=phpseven_magento2 --db-user=phpseven_mage2 --db-password=Mbalparda89jcr --admin-firstname=Magento --admin-lastname=User --admin-email=user@example.com --admin-user=admin --admin-password=admin123 --language=en_US --currency=USD --timezone=America/Chicago --use-rewrites=1
#1450717216
php magento setup:install --base-url=http://http://php7.magento2-demo.nexcess.net --db-host=localhost --db-name=phpseven_magento2 --db-user=phpseven_mage2 --db-password=Mfbalparda89jcr --admin-firstname=Magento --admin-lastname=User --admin-email=user@example.com --admin-user=admin --admin-password=admin123 --language=en_US --currency=USD --timezone=America/Chicago --use-rewrites=1
#1450717307
ls
#1450717309
cd ..
#1450717309
ls
#1450794087
service httpd status
#1450794092
cd public_html/
#1450794093
ls
#1450794098
exit
#1450794504
ls
#1450794506
cd public_html/
#1450794508
rm -rf *
#1450794524
 wget  https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.0+Samples.zip
#1450794839
unzip Magento-CE-2.0.0+Samples.zip 
#1450794950
sed -i 's/0770/0775/' vendor/magento/framework/Filesystem/DriverInterface.php
#1450795002
find . -type d -exec chmod 2775 {} \;
#1450795018
find . -type f -exec chmod 0664 {} \;
#1450795045
exit
#1450795293
cd public_html/
#1450795320
php bin/magento set:deploy
#1450795369
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:show
#1450795378
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set production
#1450795417
ls
#1450795426
exit
#1450795450
cd public_html/
#1450795452
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:show
#1450795458
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set production
#1450795477
chmod 755 -R pub
#1450795487
xit
#1450795488
exit
#1450795568
ls
#1450795570
ll
#1450795573
cd public_html/
#1450795573
l
#1450795574
ls
#1450795576
ll
#1450795601
exit
#1450795825
ls
#1450795828
cd public_html/
#1450795833
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set production
#1450795848
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:show
#1450795855
php -d memory_limit=3024M -d disable_functions=0 bin/magento d
#1450795858
php -d memory_limit=3024M -d disable_functions=0 bin/magento de
#1450795860
ls
#1450795864
rm -rf *
#1450795868
exit
#1450795891
ls
#1450795893
cd public_html/
#1450795896
 wget  https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.0+Samples.zip
#1450796213
ls
#1450796215
exit
#1450796309
ls
#1450796310
cd public_html/
#1450796311
ls
#1450796315
unzip Magento-CE-2.0.0+Samples.zip 
#1450796395
vi vendor/magento/framework/Filesystem/DriverInterface.php
#1450796523
find . -type f -exec chmod 644 {} \;
#1450796553
find . -type d -exec chmod 755 {} \;
#1450796701
exit
#1450797070
mkdir /home/phpseven/php7.magento2-demo.nexcess.net/html/var/generation
#1450797369
cd public_html/
#1450797504
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:show
#1450797512
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set production
#1450797602
cd pub/static/
#1450797602
ls
#1450797604
ll
#1450797607
cd frontend/
#1450797608
ll
#1450797611
cd Magento/
#1450797611
s
#1450797612
ls
#1450797614
cd ..
#1450797644
cd pub/
#1450797644
ls
#1450797689
ll
#1450797700
cd st
#1450797701
ll
#1450797703
cd static/
#1450797704
ls
#1450797705
ll
#1450797707
cd frontend/
#1450797708
ll
#1450797710
cd Magento/
#1450797711
ls
#1450797712
ll
#1450797713
cd luma/
#1450797714
ll
#1450797715
cd en_US/
#1450797716
ll
#1450797725
cd css/
#1450797726
ls
#1450797727
ll
#1450797729
cd ..
#1450797730
c ..
#1450797731
cd ..
#1450797738
find . -type d -exec chmod 755 {} \;
#1450797741
find . -type f -exec chmod 644 {} \;
#1450797759
cd ..
#1450797762
find . -type d -exec chmod 755 {} \;
#1450797775
find . -type f -exec chmod 644 {} \;
#1450797802
cd pub/
#1450797806
chmod 777 -% *
#1450797810
chmod 777 -R *
#1450797909
cd ..
#1450797911
php -v
#1450797917
php bin/magento reindex
#1450797923
php bin/magento indexer:reindex
#1450797929
php bin/magento cache
#1450797933
php bin/magento cache:flush
#1450797937
php bin/magento cache:clean
#1450797941
php bin/magento cache:status
#1450798017
vi nexinfo.php
#1450798108
vi .htaccess
#1450799086
crontab -e
#1450799098
vi .bashrc 
#1450876889
php -v
#1450876909
exit
#1450876919
ls
#1450876929
vim .bashrc 
#1450876943
exit
#1453478063
cd public_html/
#1453478072
php70u bin/magento reindex
#1453478078
php70u bin/magento indexer:reindex
#1453478384
crontab -e
#1453478434
php70u bin/magento indexer:reindex
#1453478481
exit
#1453478603
cd public_html/
#1453478605
php70u bin/magento indexer:reindex
#1453478654
exit
#1453470659
crontab -e
#1453470817
php -v
#1453470827
crontab -e
#1453470845
php70 -v
#1453470850
php70u -v
#1453470856
crontab -e
#1454940530
php -v
#1454940535
quit
#1454940536
exit
#1455033998
cd public_html/app/etc/
#1455033999
vi env.php 
#1455034023
php -v env.php 
#1455034029
php -l env.php 
#1455034082
redis-cli monitor
#1455034147
vi env.php 
#1455034288
cd ..
#1455034289
exit
#1455548388
cd public_html/
#1455548390
ls
#1455548410
rm -rf * .htaccess .php_cs .travis.yml .htaccess.sample 
#1455548414
ls
#1455548421
exit
#1455548474
ls
#1455548484
mv Magento-CE-2.0.2+sample_data.zip php7.magento2-demo.nexcess.net/html/
#1455548486
cd public_html/
#1455548488
unzip Magento-CE-2.0.2+sample_data.zip 
#1455548517
ls
#1455548543
vi vendor/magento/framework/Filesystem
#1455548555
vi vendor/magento/framework/Filesystem/DriverInterface.php 
#1455548603
ls
#1455548604
cd ..
#1455548605
ls
#1455548685
ll
#1455548765
exit
#1455549376
cd public_html/
#1455549376
ls
#1455549380
vi nexinfo.php
#1455549515
ls
#1455549522
php -vv
#1455549578
exit
#1455551696
ls
#1455551698
cd public_html/
#1455551702
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set production
#1455551791
php -d memory_limit=3024M -d disable_functions=0 bin/magento setup:static-content:deploy
#1455551866
cd pub/
#1455551875
chmod 755 -R *
#1455551881
cd ../lib/web/
#1455551891
chmod 755 -R *
#1455551944
cd ../../
#1455551946
find . -type d -exec chmod 2775 {} \;
#1455551963
 find . -type f -exec chmod 0664 {} \;
#1455552108
vi .htaccess
#1455555286
cd public_html/
#1455555287
cd app/etc/
#1455555287
ls
#1455555314
exit
#1455555511
cd public_html/
#1455555515
vi app/etc/env.php 
#1455555571
cd app/etc/
#1455555574
php -l env.php 
#1455555590
cd ..
#1455555593
cd var/
#1455555593
ls
#1455555596
cd report/l
#1455555598
cd report/
#1455555599
ls
#1455555603
vi 171755559193 
#1455555621
cd ../../app/etc/
#1455555623
vi env.php 
#1455555646
cd ../../repo
#1455555646
ls
#1455555648
cd ../../
#1455555649
ls
#1455555651
cd var/report/
#1455555656
vi 850044494952 
#1455555666
cd ..
#1455555669
cd app/etc/
#1455555672
vi env.php 
#1455555698
php -l env.php 
#1455555706
exit
#1455556480
cd public_html/
#1455556482
cd app/etc/
#1455556483
vi env.php 
#1455556525
php -l env.php 
#1455556538
vi env.php 
#1455556627
php -l env.php 
#1455556634
vi env.php 
#1455556697
php -l env.php 
#1455556702
vi env.php 
#1455556724
php -l env.php 
#1455556850
exit
#1455642236
cd public_html/
#1455642239
vi index.php 
#1455642308
exit
#1456499106
cd public_html/
#1456499109
php bin/magento setup:di:compile
#1456499129
rm -rf /chroot/home/phpseven/php7.magento2-demo.nexcess.net/html/var/di
#1456499132
php bin/magento setup:di:compile
#1456499244
exit
#1459794384
ls
#1459794386
cd public_html/
#1459794392
php bin/magento mode
#1459794401
php bin/magento deploy:mode:show
#1459795285
ls
#1459868508
cd public_html/
#1459868513
php bin/magento mode
#1459868520
php bin/magento d:m:s developer
#1459868526
php bin/magento d:m:set developer
#1459868730
ls
#1459868731
cd var/
#1459868742
vi .update_cronjob_status 
#1459868979
ls
#1459868980
ll
#1459868990
exit
#1459869449
ls
#1459869451
cd public_html/
#1459869590
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set production
#1459869790
exit
#1459869902
cd public_html/
#1459869907
find . -type f -exec chmod 0664 {} \;
#1459869934
find . -type d -exec chmod 2775 {} \;
#1459869954
cd pub/
#1459869958
chmod 755 -R *
#1459869959
cd ..
#1459869962
cd lib/
#1459869963
chmod 755 -R *
#1459869964
cd web/
#1459869965
chmod 755 -R *
#1459869993
cd ..
#1459869995
cd pub/
#1459869996
ll
#1459869998
cd media/
#1459869998
ls
#1459869999
ll
#1459870001
cd ..
#1459870008
find . -type f -exec chmod 0664 {} \;
#1459870023
chmod 755 -R *
#1459870074
find . -type d -exec chmod 2775 {} \;
#1459870100
cd ..
#1459870126
find . -type d -exec chmod 2775 {} \;
#1459870165
cd pub/
#1459870168
find . -type d -exec chmod 2775 {} \;
#1459870255
cd ..
#1459870263
chmod g+rwx -R pub/
#1459870269
chmod o+rwx -R pub/
#1459870341
find . -type d -exec chmod 2775 {} \+
#1459870366
php bin/magento cache
#1459870371
php bin/magento cache:f
#1459870373
php bin/magento cache:c
#1459870379
php bin/magento cache:s

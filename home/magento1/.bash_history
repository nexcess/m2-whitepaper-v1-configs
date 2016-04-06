#1448032754
php -v
#1448032860
curl -k https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/info/help
#1448032864
cd public_html/
#1448032864
ls
#1448032872
rm index.html robots.txt 
#1448032920
curl -k https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/files
#1448032929
curl -k https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/info/help
#1448032939
curl -k https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.comhttps://MAG_ID:TOKEN@www.magentocommer
#1448032980
wget  https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.0+Samples.zip
#1448033307
unzip Magento-CE-2.0.0+Samples.zip 
#1448033373
vi vendor/magento/framework/Filesystem/DriverInterface.php
#1448046369
cd public_html/
#1448046377
rm -rf * .htaccess .htaccess.sample  .travis.yml .php_cs 
#1448046382
wget  https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.0+Samples.zip
#1448046717
unzip Magento-CE-2.0.0+Samples.zip 
#1448047188
exit
#1448047220
cd public_html/
#1448047225
mv Magento-CE-2.0.0+Samples.zip ../
#1448047228
rm -rf * .htaccess .htaccess.sample  .travis.yml .php_cs 
#1448047239
mv /home/magento1/magento2-demo.nexcess.net/Magento-CE-2.0.0+Samples.zip .
#1448047243
unzip Magento-CE-2.0.0+Samples.zip 
#1448047736
vi vendor/magento/framework/Filesystem/DriverInterface.php
#1448047759
exit
#1448235964

#1448235977
cd public_html/
#1448236024
php bin/magento cache:flush 
#1448236030
php bin/magento cache:clean
#1449249980
cd public_html/
#1449249980
ls
#1449249983
cd bin/
#1449250029
php magento indexer:reindex
#1449250044
php magento cache:clear
#1449250049
php magento cache:clean
#1449250055
php magento cache:flush
#1449250059
php magento cache:status
#1449250076
cd ..
#1449250079
vi .htaccess
#1449250175
exit
#1449252660
ls
#1449252661
cd public_html/
#1449252699
vi robots.txt
#1450185198
cd public_html/
#1450185198
ls
#1450185361
php bin/magento setup:mode:set production
#1450185394
php bin/magento d:m:s
#1450185400
php bin/magento d:m:show
#1450185407
php bin/magento d:m:set production
#1450185454
php -pdisable_functions=0  bin/magento d:m:set production
#1450185460
php -p disable_functions=0  bin/magento d:m:set production
#1450185630
php  bin/magento d:m:set production
#1450185741
exit
#1450185761
cd public_html/
#1450185773
-d disabled_functions=0 php  bin/magento d:m:set production
#1450185781
php -d disabled_functions=0  bin/magento d:m:set production
#1450185797
php -d disable_functions=0  bin/magento d:m:set production
#1450186045
composer update
#1450186056
exit
#1450186285
ls
#1450186287
cd public_html/
#1450186288
ls
#1450186288
ll
#1450186313
composer update
#1450186327
composer update -d allow_url_open=On
#1450186341
composer update -d allow_url_fopen=On
#1450186346
ls
#1450186395
php bin/magento setup:mode:set default
#1450186403
php bin/magento deploy:mode:set default
#1450186410
php bin/magento deploy:mode:set development
#1450186416
php bin/magento deploy:mode:set production
#1450186452
php -d disable_functions=0 bin/magento deploy:mode:set production
#1450186518
php -d disable_functions=0 bin/magento setup:di:compile-multi-tenant
#1450186575
service apache2 stop
#1450186578
srvice apache stop
#1450186582
service httpd restart
#1450186637
php -d memory_limit=2024M disable_functions=0 bin/magento setup:di:compile-multi-tenant
#1450186645
php -d memory_limit=2024M -d disable_functions=0 bin/magento setup:di:compile-multi-tenant
#1450186700
php -d disable_functions=0 bin/magento deploy:mode:set production
#1450186787
php -d memory_limit=2024M -d disable_functions=0 bin/magento deploy:mode:set production
#1450186863
cd ..
#1450186863
ls
#1450186865
cd var/
#1450186865
ls
#1450186867
exit
#1450186942
php -v
#1450186969
cd public_html/
#1450186996
php -d memory_limit=2024M -d disable_functions=0 bin/magento setup:di:compile-multi-tenant 2>&1
#1450187066
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set production
#1450187197
chmod 755 -R pub/
#1450187206
ll
#1450187216
chmod 777 -R pub/
#1450187251
cd pub/
#1450187252
ls
#1450187258
chmod 777 -R static
#1450282612
ls
#1450282615
cd bin/
#1450282615
ls
#1450282635
php -f magento
#1450282656
cd ..
#1450282656
ls
#1450282660
cd pub/
#1450282660
ls
#1450282667
mv static static.disabled
#1450282668
cd ..
#1450282669
ls
#1450282670
cd bin/
#1450282670
ls
#1450282706
php -f magento setup:static-content:deploy
#1450282984
php -f magento cache:flush --all
#1450283002
php -f magento cache:flush
#1450283060
php -f magento cache:clean
#1450283128
php -f magento deploy:mode:show
#1450283147
cd ..
#1450283147
ls
#1450283148
cd var/
#1450283148
ls
#1450283157
mv generation/ generation.disabled
#1450283164
ls
#1450282282
cd public_html/
#1450282288
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set production
#1450282406
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set default
#1450282532
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set developer
#1450282539
cd var/
#1450282540
ls
#1450282551
cd ..
#1450282551
ls
#1450282554
cd var/
#1450282557
ls
#1450282562
rm -rf generation/
#1450283954
cd public_html/
#1450283970
rm -rf * .htaccess .htaccess.sample .travis.yml .php_cs 
#1450283972
ll
#1450283981
wget  https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.0+Samples.zip
#1450284314
unzip Magento-CE-2.0.0+Samples.zip 
#1450284612
ls
#1450284620
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set production
#1450284851
ls
#1450284853
cd pub/
#1450284853
ls
#1450284999
chmod 755 -R *
#1450285028
cd ..
#1450285035
cd lib/web/
#1450285035
ls
#1450285043
chmod 755 -R *
#1450285137
cd ../pub
#1450285139
cd ..
#1450285139
ls
#1450285141
cd ..
#1450285142
cd pub/
#1450285142
ls
#1450285142
ll
#1450285144
cd md
#1450285146
lscd media/
#1450285146
ls
#1450285147
cd media/
#1450285148
ls
#1450285150
ll
#1450285157
chmod 755 -R *
#1450285205
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:show
#1450285208
cd ..
#1450285212
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:show
#1450285267
vi .htaccess
#1450439385
cd public_html/
#1450439408
php bin/magento cache
#1450439414
php bin/magento cache:flush
#1450439420
php bin/magento cache:clean
#1450439426
php bin/magento index
#1450439434
php bin/magento indexer:r
#1450714573
exit
#1450791915
ls
#1450791917
cd public_html/
#1450791917
ls
#1450791918
cd var/
#1450791919
ls
#1450791921
cd log
#1450791922
ls
#1450791924
vi system.log 
#1450791943
vi debug.log 
#1450791970
exit
#1450792130
php -v
#1450792215
servie httpd restart
#1450792220
service httpd restart
#1450792224
exi
#1450792226
exit
#1450793020
cd public_html/var/report/
#1450793021
ls
#1450793164
vi 696380628937
#1450793194
php -v
#1450793225
cd ..
#1450793225
ls
#1450793228
cd ..
#1450793230
vi nexinfo.php 
#1450793286
ls
#1450794348
cd public_html/
#1450794348
ls
#1450794498
exit
#1450796218
ls
#1450796219
cd public_html/
#1450796220
ls
#1450796227
cd pub/media/
#1450796227
ls
#1450796228
ll
#1450796236
find . -type f -exec chmod 0664 {} \;
#1450796245
find . -type d -exec chmod 2775 {} \;
#1450796247
cd ..
#1450796249
find . -type d -exec chmod 2775 {} \;
#1450796253
find . -type f -exec chmod 0664 {} \;
#1450796306
exit
#1450796704
cd public_html/
#1450796707
php bin/magento indexer:r
#1450796717
php -v
#1450796727
php bin/magento cache:clean
#1450796731
php bin/magento cache:flush
#1450876975
php -v
#1450877005
exit
#1452024631
ls
#1452024778
cd test.magento2-demo.nexcess.net/
#1452024788
wget https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.0+Samples.zip
#1452024794
wget https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.0.zip
#1452024894
unzip Magento-CE-2.0.0.zip 
#1452024917
vi vendor/magento/framework/Filesystem/DriverInterface.php
#1452024958
;s
#1452024959
ls
#1452024964
cd ..
#1452024965
ls
#1452024967
cd test.magento2-demo.nexcess.net/
#1452024967
ls
#1452024971
cd html/
#1452024976
mv ../Magento-CE-2.0.0.zip .
#1452024981
unzip Magento-CE-2.0.0.zip 
#1452025002
vi vendor/magento/framework/Filesystem/DriverInterface.php
#1452025244
uptim
#1452025246
uptime
#1452025372
exit
#1453468909
ls
#1453468911
cd public_html/
#1453468916
crontab -e
#1453468986
cd /home/magento1/public_html/
#1453469085
crontab -e
#1453469323
chmod 777 -R var/
#1453469338
sudo chmod 777 -R var/
#1453469409
exit
#1453471578
vi /home/magento1/magento2-demo.nexcess.net/html/vendor/magento/magento2-base/dev/tests/integration/.gitignore
#1453476067
cd public_html/
#1453476192
exit
#1453476216
cd public_html/
#1453476220
php bin/magento setup:rollback -d var/backups/1453470519_db.gz 
#1453476294
php bin/magento setup:rollback --db-file="var/backups/1453470519_db.gz"
#1453476305
cd var/backups/
#1453476305
ls
#1453476306
ll
#1453476310
ll -hj
#1453476311
ll -h
#1453476326
rm 1453470519_db
#1453476329
c ..
#1453476330
cd ..
#1453476336
php bin/magento setup:rollback --db-file="var/backups/1453470519_db.gz"
#1454078190
ls
#1454078202
cd public_html/
#1454078202
ls
#1454078206
cd var/
#1454078206
ls
#1454078207
cd log/
#1454078208
ls
#1454078209
ll
#1454078213
vi system.log 
#1454078248
vi exception.log 
#1454078281
vi debug.log 
#1454078301
vi install.log 
#1454078316
exit
#1454078355
cd public_html/var/
#1454078356
ls
#1454078357
cd log/
#1454078357
ls
#1454078360
vi install.log 
#1454078379
vi system.log 
#1454078399
vi exception.log 
#1454078457
vi debug.log 
#1454078491
vi system.log 
#1454080690
cd public_html/
#1454080691
cd va
#1454080697
cd var/
#1454080697
ls
#1454080708
cd ..
#1454080708
ls
#1454080711
cd var/
#1454080711
ls
#1454080730
vi update_status.log 
#1454080744
mv update_status.log _update_status.log 
#1454080785
ls
#1454080795
cd ..
#1454080796
ls
#1454080799
ll
#1454080804
cd var/
#1454080804
ls
#1454080828
cd set
#1454080833
cd ../setup/
#1454080833
ls
#1454080836
cd pub/
#1454080837
ls
#1454080839
cd ..
#1454080865
cd public_html/
#1454080867
cd var/
#1454080867
ls
#1454080870
cd cache/
#1454080870
ls
#1454080873
rm -rf *
#1454080874
ls
#1454080875
cd ..
#1454080876
ls
#1454080879
cd di/
#1454080880
ls
#1454080883
cd ../generation/
#1454080883
ls
#1454080886
cd ..
#1454080888
cd page_cache/
#1454080888
ls
#1454080890
rm -rf *
#1454080893
cd ..
#1454080893
ls
#1454080899
rm _update_status.log 
#1454080903
cd tmp/
#1454080903
ls
#1454080905
cd ..
#1454080906
cd view_preprocessed/
#1454080907
sl
#1454080908
ls
#1454080943
cd ..
#1454080944
ls
#1454080949
cd report/
#1454080949
ls
#1454080951
ll
#1454081147
ls
#1454081148
cd .
#1454081150
cd ..
#1454081152
ls
#1454081154
ll
#1454081173
rm .update_in_progress.flag 
#1450885249
cd public_html/
#1450885326
cd app/etc/
#1450885326
ll
#1450885511
vi env.php 
#1454953156
cd public_html/
#1454953158
vi .htaccess
#1454953210
r
#1454953212
exit
#1455031735
cd public_html/app/etc/
#1455031738
vi env.php 
#1455031835
cd ../../var/
#1455031835
ls
#1455031838
cd log
#1455031838
ls
#1455031839
ll
#1455031846
cd ..
#1455031847
cd report/
#1455031847
ls
#1455031849
ll
#1455031864
rm -rf *
#1455031866
ls
#1455031873
cd ..
#1455031873
ls
#1455031875
cd log
#1455031875
ls
#1455031877
ll
#1455031884
date
#1455031895
cd ..
#1455031895
ls
#1455031898
cd ../app/etc/
#1455031901
php -l env.php 
#1455031909
vi env.php 
#1455032045
php -l env.php 
#1455032078
redis-cli ping
#1455032123
php -m | grep redis
#1455032127
php -v
#1455032187
redis-cli MONITOR
#1455032409
nkredis list
#1455032417
exit
#1455033203
ls
#1455033206
cd public_html/
#1455033207
ls
#1455033210
cd app/etc/
#1455033211
vi env.php 
#1455033293
exit
#1455033438
cd public_html/app/etc/
#1455033440
vi env.php 
#1455033642
exit
#1455043472
ls
#1455043476
cd public_html/
#1455043476
ls
#1455043482
ll
#1455043506
cd var/
#1455043540
rm .maintenance.flag 
#1455043548
cd ..
#1455043555
rm -rf *
#1455043572
wget https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.2+Samples.zip
#1455043969
cd public_html/
#1455043973
wget https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.0+Samples.zip
#1455044012
ls
#1455044070
curl -k https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/files
#1455044100
wget https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.2+Samples.zip
#1455044117
curl -k https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/files
#1455044195
3) curl -k https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/info/help
#1455044204
curl -k https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/info/help
#1455044223
curl -k https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/info/
#1455044249
curl -k https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/info/files
#1455044273
wget https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.2+sample_data.zip
#1455044798
cd public_html/
#1455044798
ls
#1455044802
rm Magento-CE-2.0.2+sample_data.zip 
#1455044812
clear
#1455044819
wget https://MAG001697831:43eb7f6d9b956c1dec940edc166934581a32bbf5@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.2+Samples.zip
#1455044918
wget https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/file/Magento-CE-2.0.2+sample_data.zip
#1455045301
unzip Magento-CE-2.0.2+sample_data.zip 
#1455046790
cd public_html/
#1455046802
rm -rf *
#1455046807
exit
#1455047471
ls
#1455047483
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:show
#1455047486
cd public_html/
#1455047490
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set production
#1455047686
php -f magento setup:static-content:deploy
#1455047692
php -f bin/magento setup:static-content:deploy
#1455047756
cd pub/
#1455047769
chmod 755 -R *
#1455047776
cd lib
#1455047776
ls
#1455047777
ll
#1455047784
cd ..
#1455047788
cd public_html/lib/
#1455047788
ls
#1455047790
cd web
#1455047801
chmod 755 -R *
#1455047853
cd ..
#1455047856
find . -type d -exec chmod 2775 {} \;
#1455047870
find . -type f -exec chmod 0664 {} \;
#1455047902
php bin/magento cache:flush
#1455047907
php bin/magento cache:clean
#1455048031
php magento indexer:reindex
#1455048037
php bin/magento indexer:reindex
#1455048060
crontab -e
#1455048145
php magento indexer:reindex
#1455048155
vi app/etc/env.php 
#1455048186
php -l app/etc/env.php 
#1455048195
cd app/etc/
#1455048196
vi env.php 
#1455048223
php -l env.php 
#1455048231
vi env.php 
#1455048302
php -l env.php 
#1455048323
vi env.php 
#1455048329
php -l env.php 
#1455111888
php -v
#1455111892
php -i |grep opcache
#1455111898
exit
#1455111912
php -i |grep opcache
#1455112560
exit
#1455272143
cd public_html/
#1455272146
vi .htaccess
#1455272193
php bin/magento mode
#1455272208
php bin/magento deploy:mode:show
#1455272216
vi .htaccess
#1455272227
varnishlog
#1455272235
exit
#1455279934
ls
#1455279936
cd public_html/
#1455279936
ls
#1455279938
cd app/etc/
#1455279940
vi env.php 
#1455279969
php -l env.php 
#1455279974
vi env.php 
#1455280026
php -l env.php 
#1455280045
vi env.php 
#1455280065
php -l env.php 
#1455280068
vi env.php 
#1455280102
php -l env.php 
#1455280104
vi env.php 
#1455280131
php -l env.php 
#1455280135
cd ..
#1455280138
php bin/magento cache:flush
#1455280193
php bin/magento cache:clean
#1455280196
exit
#1455538641
ls
#1455538643
cd public_html/
#1455538709
php bin/magento setup:performance:generate-fixtures
#1455538820
vi small.xml
#1455538831
php bin/magento setup:performance:generate-fixtures small.xml 
#1455539143
exit
#1455626963
cd public_html/
#1455626965
vi .htaccess
#1455654931
ls
#1455654940
ll
#1455654960
ls
#1455655025
exit
#1455654397
ls
#1455654402
cd m1internal.nextmp.net/html/
#1455654407
curl -k https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/info/files
#1455654428
wget https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/file/magento-1.9.2.3.zip
#1455654439
cd ..
#1455654442
ls
#1455717849
cd m1internal.nextmp.net/
#1455717851
ls
#1455717852
cd ht
#1455717854
cd html
#1455717855
ls
#1455717858
wget https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/file/magento-1.9.2.3.zip
#1455717926
unzip magento-1.9.2.3.zip 
#1455717933
cd magento/
#1455717937
mv * ../
#1455717943
mv .htaccess.sample ../
#1455717950
mv .htaccess ../
#1455717951
cd ..
#1455717955
rm -rf magento
#1455717963
[Bcurl -k https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/info/files
#1455717973
curl -k https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/info/files
#1455717995
wget https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/file/magento-sample-data-1.9.1.0.zip
#1455718690
rm magento-sample-data-1.9.1.0.zip 
#1455718698
wget https://MAG001697831:790a8ea56961c793830251b2c41bca0833d010e2@www.magentocommerce.com/products/downloads/file/magento-sample-data-1.9.1.0.tar.gz
#1455719419
tar xvzf magento-sample-data-1.9.1.0.tar.gz 
#1455719427
cd magento-sample-data-1.9.1.0/
#1455719427
ls
#1455719439
cd media/
#1455719445
cp -r * ../../media/
#1455719448
cd ..
#1455719449
cd skin/
#1455719457
cp -r * ../../skin/
#1455719462
cd ..
#1455719462
ls
#1455719539
mysql -umagento1_mage1 -p
#1455719563
mysql -umagento1_mage1 -p magento1_mage1 < magento_sample_data_for_1.9.1.0.sql 
#1455719572
exit
#1455719575
cd ..
#1455719575
ls
#1455719581
rm index.html 
#1456499041
cd public_html/
#1456499053
php bin/magento setup:di:compile
#1456499071
php bin/magento setup:di:status
#1456499099
exit
#1455046827
ls
#1455046832
cd magento2-demo.nexcess.net/
#1455046832
ls
#1455046836
mv Magento-CE-2.0.2+sample_data.zip html/
#1455046838
cd html/
#1455046853
rm -rf .gitignore .htaccess.sample .htaccess .travis.yml .php_cs 
#1455046856
unzip Magento-CE-2.0.2+sample_data.zip 
#1455046911
vi vendor/magento/framework/Filesystem/DriverInterface.php
#1459522771
cd public_html/
#1459522773
nkmagento2
#1459522781
exit
#1459806300
ls
#1459806303
cd public_html/
#1459806303
top
#1459806316
cd var/
#1459806316
ls
#1459806317
ll
#1459806321
cat update_status.log 
#1459806329
ls
#1459806331
ll
#1459806351
cd ..
#1459806351
ls
#1459806384
exit
#1459806787
cd public_html/
#1459806787
ls
#1459806788
ll
#1459806790
cd var/
#1459806791
ls
#1459806792
ll
#1459806795
rm update_status.log 
#1459806800
rm .maintenance.flag 
#1459806812
nkmagento2
#1459806816
exit
#1459807045
cd public_html/
#1459807046
ls
#1459807049
php bin/magento setup:static:regenerate
#1459807076
cd var/
#1459807077
ls
#1459807081
ll
#1459807092
rm .update_*
#1459807147
cd ..
#1459807153
php bin/magento r:a
#1459807161
php bin/magento index
#1459807168
php bin/magento i:r
#1459807187
php bin/magento cache
#1459807191
php bin/magento cache:c
#1459807193
php bin/magento cache:f
#1459807207
exit
#1459868997
cd public_html/
#1459869016
php bin/magento d:m:set production
#1459869031
php -d memory_limit=3024M -d disable_functions=0 bin/magento deploy:mode:set production
#1459869195
exit
#1459869794
cd public_html/
#1459869796
find . -type f -exec chmod 0664 {} \;
#1459869834
find . -type d -exec chmod 2775 {} \;
#1459869860
ls
#1459869874
cd pub/
#1459869880
chmod 755 -R *
#1459869883
cd ..
#1459869885
cd lib/
#1459869888
chmod 755 -R *
#1459869890
cd web/
#1459869890
chmod 755 -R *
#1459869899
exit

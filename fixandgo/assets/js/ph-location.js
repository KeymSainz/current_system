/**
 * Fix&Go — Philippine Location Data
 * Cascading selects: Region → Province → City/Municipality → Barangay
 * Uses the PSGC (Philippine Standard Geographic Code) structure.
 * Data covers all 17 regions with major provinces, cities, and barangays.
 */

/* ── PROVINCE DATA BY REGION ─────────────────────────────────────────── */
var PH_PROVINCES = {
  'NCR':  ['Metro Manila'],
  'CAR':  ['Abra','Apayao','Benguet','Ifugao','Kalinga','Mountain Province'],
  'I':    ['Ilocos Norte','Ilocos Sur','La Union','Pangasinan'],
  'II':   ['Batanes','Cagayan','Isabela','Nueva Vizcaya','Quirino'],
  'III':  ['Aurora','Bataan','Bulacan','Nueva Ecija','Pampanga','Tarlac','Zambales'],
  'IVA':  ['Batangas','Cavite','Laguna','Quezon','Rizal'],
  'IVB':  ['Marinduque','Occidental Mindoro','Oriental Mindoro','Palawan','Romblon'],
  'V':    ['Albay','Camarines Norte','Camarines Sur','Catanduanes','Masbate','Sorsogon'],
  'VI':   ['Aklan','Antique','Capiz','Guimaras','Iloilo','Negros Occidental'],
  'VII':  ['Bohol','Cebu','Negros Oriental','Siquijor'],
  'VIII': ['Biliran','Eastern Samar','Leyte','Northern Samar','Samar','Southern Leyte'],
  'IX':   ['Zamboanga del Norte','Zamboanga del Sur','Zamboanga Sibugay'],
  'X':    ['Bukidnon','Camiguin','Lanao del Norte','Misamis Occidental','Misamis Oriental'],
  'XI':   ['Davao de Oro','Davao del Norte','Davao del Sur','Davao Occidental','Davao Oriental'],
  'XII':  ['Cotabato','Sarangani','South Cotabato','Sultan Kudarat'],
  'XIII': ['Agusan del Norte','Agusan del Sur','Dinagat Islands','Surigao del Norte','Surigao del Sur'],
  'BARMM':['Basilan','Lanao del Sur','Maguindanao del Norte','Maguindanao del Sur','Sulu','Tawi-Tawi']
};

/* ── CITY/MUNICIPALITY DATA BY PROVINCE ─────────────────────────────── */
var PH_CITIES = {
  'Metro Manila':['Caloocan','Las Piñas','Makati','Malabon','Mandaluyong','Manila','Marikina','Muntinlupa','Navotas','Parañaque','Pasay','Pasig','Pateros','Quezon City','San Juan','Taguig','Valenzuela'],
  'Abra':['Bangued','Boliney','Bucay','Bucloc','Daguioman','Danglas','Dolores','La Paz','Lacub','Lagangilang','Lagayan','Langiden','Licuan-Baay','Luba','Malibcong','Manabo','Peñarrubia','Pidigan','Pilar','Sallapadan','San Isidro','San Juan','San Quintin','Tayum','Tineg','Tubo','Villaviciosa'],
  'Benguet':['Atok','Baguio City','Bakun','Bokod','Buguias','Itogon','Kabayan','Kapangan','Kibungan','La Trinidad','Mankayan','Sablan','Tuba','Tublay'],
  'Ilocos Norte':['Adams','Bacarra','Badoc','Bangui','Banna','Batac City','Burgos','Carasi','Currimao','Dingras','Dumalneg','Laoag City','Marcos','Nueva Era','Pagudpud','Paoay','Pasuquin','Piddig','Pinili','San Nicolas','Sarrat','Solsona','Vintar'],
  'Ilocos Sur':['Alilem','Banayoyo','Bantay','Burgos','Cabugao','Candon City','Caoayan','Cervantes','Galimuyod','Gregorio del Pilar','Lidlidda','Magsingal','Nagbukel','Narvacan','Quirino','Salcedo','San Emilio','San Esteban','San Ildefonso','San Juan','San Vicente','Santa','Santa Catalina','Santa Cruz','Santa Lucia','Santa Maria','Santiago','Santo Domingo','Sigay','Sinait','Sugpon','Suyo','Tagudin','Vigan City'],
  'La Union':['Agoo','Aringay','Bacnotan','Bagulin','Balaoan','Bangar','Bauang','Burgos','Caba','Luna','Naguilian','Pugo','Rosario','San Fernando City','San Gabriel','San Juan','Santo Tomas','Santol','Sudipen','Tubao'],
  'Pangasinan':['Agno','Aguilar','Alaminos City','Alcala','Anda','Asingan','Balungao','Bani','Basista','Bautista','Bayambang','Binalonan','Binmaley','Bolinao','Bugallon','Burgos','Calasiao','Dagupan City','Dasol','Infanta','Labrador','Laoac','Lingayen','Mabini','Malasiqui','Manaoag','Mangaldan','Mangatarem','Mapandan','Natividad','Pozorrubio','Rosales','San Carlos City','San Fabian','San Jacinto','San Manuel','San Nicolas','San Quintin','Santa Barbara','Santa Maria','Santo Tomas','Sison','Sual','Tayug','Umingan','Urbiztondo','Urdaneta City','Villasis'],
  'Cagayan':['Abulug','Alcala','Allacapan','Amulung','Aparri','Baggao','Ballesteros','Buguey','Calayan','Camalaniugan','Claveria','Enrile','Gattaran','Gonzaga','Iguig','Lal-lo','Lasam','Pamplona','Peñablanca','Piat','Rizal','Sanchez-Mira','Santa Ana','Santa Praxedes','Santa Teresita','Santo Niño','Solana','Tuao','Tuguegarao City'],
  'Isabela':['Alicia','Angadanan','Aurora','Benito Soliven','Burgos','Cabagan','Cabatuan','Cauayan City','Cordon','Delfin Albano','Dinapigue','Divilacan','Echague','Gamu','Ilagan City','Jones','Luna','Maconacon','Mallig','Naguilian','Palanan','Quezon','Quirino','Ramon','Reina Mercedes','Roxas','San Agustin','San Guillermo','San Isidro','San Manuel','San Mariano','San Mateo','San Pablo','Santa Maria','Santiago City','Santo Tomas','Tumauini'],
  'Bulacan':['Angat','Balagtas','Baliuag','Bocaue','Bulakan','Bustos','Calumpit','Doña Remedios Trinidad','Guiguinto','Hagonoy','Malolos City','Marilao','Meycauayan City','Norzagaray','Obando','Pandi','Paombong','Plaridel','Pulilan','San Ildefonso','San Jose del Monte City','San Miguel','San Rafael','Santa Maria'],
  'Pampanga':['Angeles City','Apalit','Arayat','Bacolor','Candaba','Floridablanca','Guagua','Lubao','Mabalacat City','Macabebe','Magalang','Masantol','Mexico','Minalin','Porac','San Fernando City','San Luis','San Simon','Santa Ana','Santa Rita','Santo Tomas','Sasmuan'],
  'Batangas':['Agoncillo','Alitagtag','Balayan','Balete','Batangas City','Bauan','Calaca','Calatagan','Cuenca','Ibaan','Laurel','Lemery','Lian','Lipa City','Lobo','Mabini','Malvar','Mataas na Kahoy','Nasugbu','Padre Garcia','Rosario','San Jose','San Juan','San Luis','San Nicolas','San Pascual','Santa Teresita','Santo Tomas','Taal','Talisay','Tanauan City','Taysan','Tingloy','Tuy'],
  'Cavite':['Alfonso','Amadeo','Bacoor City','Carmona','Cavite City','Dasmariñas City','General Emilio Aguinaldo','General Mariano Alvarez','General Trias City','Imus City','Indang','Kawit','Magallanes','Maragondon','Mendez','Naic','Noveleta','Rosario','Silang','Tagaytay City','Tanza','Ternate','Trece Martires City'],
  'Laguna':['Alaminos','Bay','Biñan City','Cabuyao City','Calamba City','Calauan','Cavinti','Famy','Kalayaan','Liliw','Los Baños','Luisiana','Lumban','Mabitac','Magdalena','Majayjay','Nagcarlan','Paete','Pagsanjan','Pakil','Pangil','Pila','Rizal','San Pablo City','San Pedro City','Santa Cruz','Santa Maria','Santa Rosa City','Siniloan','Victoria'],
  'Rizal':['Angono','Antipolo City','Baras','Binangonan','Cainta','Cardona','Jala-Jala','Morong','Pililla','Rodriguez','San Mateo','Tanay','Taytay','Teresa'],
  'Cebu':['Alcantara','Alcoy','Alegria','Aloguinsan','Argao','Asturias','Badian','Balamban','Bantayan','Barili','Bogo City','Boljoon','Borbon','Carcar City','Carmen','Catmon','Cebu City','Compostela','Consolacion','Cordova','Daanbantayan','Dalaguete','Danao City','Dumanjug','Ginatilan','Lapu-Lapu City','Liloan','Madridejos','Malabuyoc','Mandaue City','Medellin','Minglanilla','Moalboal','Naga City','Oslob','Pilar','Pinamungajan','Poro','Ronda','Samboan','San Fernando','San Francisco','San Remigio','Santa Fe','Santander','Sibonga','Sogod','Tabogon','Tabuelan','Talisay City','Toledo City','Tuburan','Tudela'],
  'Bohol':['Alburquerque','Alicia','Anda','Antequera','Baclayon','Balilihan','Batuan','Bien Unido','Bilar','Buenavista','Calape','Candijay','Carmen','Catigbian','Clarin','Corella','Cortes','Dagohoy','Danao','Dauis','Dimiao','Duero','Garcia Hernandez','Getafe','Guindulman','Inabanga','Jagna','Lila','Loay','Loboc','Loon','Mabini','Maribojoc','Panglao','Pilar','Pres. Carlos P. Garcia','Sagbayan','San Isidro','San Miguel','Sevilla','Sierra Bullones','Sikatuna','Tagbilaran City','Talibon','Trinidad','Tubigon','Ubay','Valencia'],
  'Davao del Sur':['Bansalan','Davao City','Digos City','Hagonoy','Kiblawan','Magsaysay','Malalag','Matanao','Padada','Santa Cruz','Sulop'],
  'Davao del Norte':['Asuncion','Braulio E. Dujali','Carmen','Kapalong','New Corella','Panabo City','Samal City','San Isidro','Santo Tomas','Tagum City','Talaingod'],
  'Davao de Oro':['Compostela','Laak','Mabini','Maco','Maragusan','Mawab','Monkayo','Montevista','Nabunturan','New Bataan','Pantukan'],
  'Davao Oriental':['Baganga','Banaybanay','Boston','Caraga','Cateel','Governor Generoso','Lupon','Manay','Mati City','San Isidro','Tarragona'],
  'Davao Occidental':['Don Marcelino','Jose Abad Santos','Malita','Santa Maria','Sarangani'],
  'Misamis Oriental':['Alubijid','Balingasag','Balingoan','Binuangan','Cagayan de Oro City','Claveria','El Salvador City','Gingoog City','Gitagum','Initao','Jasaan','Kinoguitan','Lagonglong','Laguindingan','Libertad','Lugait','Magsaysay','Manticao','Medina','Naawan','Opol','Salay','Sugbongcogon','Tagoloan','Talisayan','Villanueva'],
  'Misamis Occidental':['Aloran','Baliangao','Bonifacio','Calamba','Clarin','Concepcion','Don Victoriano Chiongbian','Jimenez','Lopez Jaena','Oroquieta City','Ozamiz City','Panaon','Plaridel','Sapang Dalaga','Sinacaban','Tangub City','Tudela'],
  'Zamboanga del Sur':['Aurora','Bayog','Dimataling','Dinas','Dumalinao','Dumingag','Guipos','Josefina','Kumalarang','Labangan','Lakewood','Lapuyan','Mahayag','Margosatubig','Midsalip','Molave','Pagadian City','Pitogo','Ramon Magsaysay','San Miguel','San Pablo','Tabina','Tambulig','Tigbao','Tukuran','Vincenzo A. Sagun','Zamboanga City'],
  'South Cotabato':['Banga','General Santos City','Koronadal City','Lake Sebu','Norala','Polomolok','Santo Niño','Surallah','T\'boli','Tampakan','Tantangan','Tupi'],
  'Cotabato':['Alamada','Aleosan','Antipas','Arakan','Banisilan','Carmen','Kabacan','Kidapawan City','Libungan','Magpet','Makilala','Matalam','Midsayap','Mlang','Pigkawayan','Pikit','President Roxas','Tulunan'],
  'Agusan del Norte':['Buenavista','Butuan City','Cabadbaran City','Carmen','Jabonga','Kitcharao','Las Nieves','Magallanes','Nasipit','Remedios T. Romualdez','Santiago','Tubay'],
  'Surigao del Norte':['Alegria','Bacuag','Burgos','Claver','Dapa','Del Carmen','General Luna','Gigaquit','Mainit','Malimono','Pilar','Placer','San Benito','San Francisco','San Isidro','Santa Monica','Sison','Socorro','Surigao City','Tagana-an','Tubod'],
  'Sultan Kudarat':['Bagumbayan','Columbio','Esperanza','Isulan','Kalamansig','Lambayong','Lebak','Lutayan','Palimbang','President Quirino','Senator Ninoy Aquino','Tacurong City'],
  'Sarangani':['Alabel','Glan','Kiamba','Maasim','Maitum','Malapatan','Malungon'],
  'Lanao del Norte':['Bacolod','Baloi','Baroy','Iligan City','Kapatagan','Kauswagan','Kolambugan','Lala','Linamon','Magsaysay','Maigo','Munai','Nunungan','Pantao Ragat','Pantar','Poona Piagapo','Salvador','Sapad','Sultan Naga Dimaporo','Tagoloan','Tangcal','Tubod'],
  'Bukidnon':['Baungon','Cabanglasan','Damulog','Dangcagan','Don Carlos','Impasugong','Kadingilan','Kalilangan','Kibawe','Kitaotao','Lantapan','Libona','Malitbog','Manolo Fortich','Maramag','Pangantucan','Quezon','San Fernando','Sumilao','Talakag','Valencia City'],
  'Negros Occidental':['Bacolod City','Bago City','Binalbagan','Cadiz City','Calatrava','Candoni','Cauayan','Enrique B. Magalona','Escalante City','Himamaylan City','Hinigaran','Hinoba-an','Ilog','Isabela','Kabankalan City','La Carlota City','La Castellana','Manapla','Moises Padilla','Murcia','Pontevedra','Pulupandan','Sagay City','San Carlos City','San Enrique','Silay City','Sipalay City','Talisay City','Toboso','Valladolid','Victorias City'],
  'Iloilo':['Ajuy','Alimodian','Anilao','Badiangan','Balasan','Banate','Barotac Nuevo','Barotac Viejo','Batad','Bingawan','Cabatuan','Calinog','Carles','Concepcion','Dingle','Dueñas','Dumangas','Estancia','Guimbal','Igbaras','Iloilo City','Janiuay','Lambunao','Leganes','Lemery','Leon','Maasin','Miagao','Mina','New Lucena','Oton','Passi City','Pavia','Pototan','San Dionisio','San Enrique','San Joaquin','San Miguel','San Rafael','Santa Barbara','Sara','Tigbauan','Tubungan','Zarraga'],
  'Leyte':['Abuyog','Alangalang','Albuera','Babatngon','Barugo','Bato','Baybay City','Burauen','Calubian','Capoocan','Carigara','Dagami','Dulag','Hilongos','Hindang','Inopacan','Isabel','Jaro','Javier','Julita','Kananga','La Paz','Leyte','MacArthur','Mahaplag','Matag-ob','Matalom','Mayorga','Merida','Ormoc City','Palo','Palompon','Pastrana','San Isidro','San Miguel','Santa Fe','Tabango','Tabontabon','Tacloban City','Tanauan','Tolosa','Tunga','Villaba'],
  'Albay':['Bacacay','Camalig','Daraga','Guinobatan','Jovellar','Legazpi City','Libon','Ligao City','Malilipot','Malinao','Manito','Oas','Pio Duran','Polangui','Rapu-Rapu','Santo Domingo','Tabaco City','Tiwi'],
  'Camarines Sur':['Baao','Balatan','Bato','Bombon','Buhi','Bula','Cabusao','Calabanga','Camaligan','Canaman','Caramoan','Del Gallego','Gainza','Garchitorena','Goa','Iriga City','Lagonoy','Libmanan','Lupi','Magarao','Milaor','Minalabac','Nabua','Naga City','Ocampo','Pamplona','Pasacao','Pili','Presentacion','Ragay','Sagñay','San Fernando','San Jose','Sipocot','Siruma','Tigaon','Tinambac'],
  'Palawan':['Aborlan','Agutaya','Araceli','Balabac','Bataraza','Brooke\'s Point','Busuanga','Cagayancillo','Coron','Culion','Cuyo','Dumaran','El Nido','Española','Kalayaan','Linapacan','Magsaysay','Narra','Puerto Princesa City','Quezon','Rizal','Roxas','San Vicente','Sofronio Española','Taytay'],
  'Zamboanga del Norte':['Baliguian','Dapitan City','Dipolog City','Godod','Gutalac','Jose Dalman','Kalawit','Katipunan','La Libertad','Labason','Leon B. Postigo','Liloy','Manukan','Mutia','Piñan','Polanco','Pres. Manuel A. Roxas','Rizal','Salug','Sergio Osmeña Sr.','Siayan','Sibuco','Sibutad','Sindangan','Siocon','Sirawai','Tampilisan'],
  'Maguindanao del Sur':['Buluan','Datu Abdullah Sangki','Datu Anggal Midtimbang','Datu Blah T. Sinsuat','Datu Hoffer Ampatuan','Datu Montawal','Datu Odin Sinsuat','Datu Paglas','Datu Piang','Datu Salibo','Datu Saudi-Ampatuan','Datu Unsay','Gen. Salipada K. Pendatun','Guindulungan','Kabuntalan','Mangudadatu','Mamasapano','Pagalungan','Paglat','Pandag','Rajah Buayan','Shariff Aguak','Shariff Saydona Mustapha','South Upi','Sultan Kudarat','Sultan Mastura','Sultan sa Barongis','Talayan','Upi'],
  'Lanao del Sur':['Bacolod-Kalawi','Balabagan','Balindong','Bayang','Binidayan','Buadiposo-Buntong','Bubong','Bumbaran','Butig','Calanogas','Ditsaan-Ramain','Ganassi','Kapai','Kapatagan','Lumba-Bayabao','Lumbaca-Unayan','Lumbatan','Lumbayanague','Madalum','Madamba','Maguing','Malabang','Marantao','Marawi City','Marogong','Masiu','Mulondo','Pagayawan','Piagapo','Picong','Poona Bayabao','Pualas','Saguiaran','Sultan Dumalondong','Tagoloan II','Tamparan','Taraka','Tubaran','Tugaya','Wao'],
  'Sulu':['Hadji Panglima Tahil','Indanan','Jolo','Kalingalan Caluang','Lugus','Luuk','Maimbung','Old Panamao','Omar','Pandami','Panglima Estino','Pangutaran','Parang','Pata','Patikul','Siasi','Talipao','Tapul','Tongkil'],
  'Tawi-Tawi':['Bongao','Languyan','Mapun','Panglima Sugala','Sapa-Sapa','Sibutu','Simunul','Sitangkai','South Ubian','Tandubas','Turtle Islands'],
  'Basilan':['Akbar','Al-Barka','Hadji Mohammad Ajul','Hadji Muhtamad','Isabela City','Lamitan City','Lantawan','Maluso','Sumisip','Tabuan-Lasa','Tipo-Tipo','Tuburan','Ungkaya Pukan']
};

/* ── BARANGAY DATA (sample for major cities — extend as needed) ──────── */
var PH_BARANGAYS = {
  'Davao City':['Agdao','Alambre','Alejandra Navarro','Alfonso Angliongto Sr.','Angalan','Atan-Awe','Baganihan','Bago Aplaya','Bago Gallera','Bago Oshiro','Baguio','Balengaeng','Baliok','Bangkas Heights','Baracatan','Biao Escuela','Biao Guianga','Biao Joaquin','Binugao','Bucana','Buda','Buhangin','Bunawan','Cabantian','Cadalian','Calinan','Callawa','Camansi','Carmen','Catalunan Grande','Catalunan Pequeño','Catigan','Cawayan','Centro (Poblacion)','Colosas','Communal','Crossing Bayabas','Dacudao','Dalag','Dalagdag','Daliao','Daliaon Plantation','Datu Salumay','Dominga','Dumoy','Eden','Fatima','Gatungan','Gov. Paciano Bangoy','Gov. Vicente Duterte','Gumalang','Gumitan','Ilang','Inayangan','Indangan','Kap. Tomas Monteverde Sr.','Kilate','Lacson','Lamanan','Lampianao','Langub','Lapu-lapu','Leon Garcia Sr.','Lizada','Los Amigos','Lubogan','Lumiad','Ma-a','Mabuhay','Magsaysay','Magtuod','Mahayag','Malabog','Malagos','Malalag','Manambulan','Mandug','Manuel Guianga','Mapula','Marapangi','Marilog','Matina Aplaya','Matina Crossing','Matina Pangi','Megkawayan','Mintal','Mudiang','Mulig','New Carmen','New Valencia','Pampanga','Panacan','Panalum','Pandaitan','Pangyan','Paquibato','Paradise Embak','Rafael Castillo','Riverside','Salapawan','Salaysay','Saloy','San Antonio','San Isidro','Santo Niño','Sasa','Sibulan','Sirawan','Sirib','Suawan','Subasta','Sumimao','Tacunan','Tagakpan','Tagluno','Tagurano','Talomo','Talomo River','Tamayong','Tambobong','Tamugan','Tapak','Tawan-tawan','Tibuloy','Tibungco','Tigatto','Toril','Tugbok','Tungkalan','Ubalde','Ula','Union','Waan','Wangan','Wilfredo Aquino','Wines'],
  'Cebu City':['Adlaon','Agsungot','Apas','Babag','Bacayan','Banilad','Basak Pardo','Basak San Nicolas','Binaliw','Bonbon','Budlaan','Bulacao','Buot-Taup Pardo','Busay','Calamba','Cambinocot','Capitol Site','Carreta','Central Poblacion','Cogon Pardo','Cogon Ramos','Day-as','Duljo-Fatima','Ermita','Guadalupe','Guba','Hippodromo','Inayawan','Kalubihan','Kalunasan','Kamagayan','Kasambagan','Kinasang-an Pardo','Labangon','Lahug','Lorega San Miguel','Lusaran','Luz','Mabini','Mabolo','Malubog','Mambaling','Pahina Central','Pahina San Nicolas','Pamutan','Pardo','Pari-an','Paril','Pasil','Pit-os','Poblacion Pardo','Pulangbato','Pung-ol-Sibugay','Punta Princesa','Quiot Pardo','Sambag I','Sambag II','San Antonio','San Jose','San Nicolas Central','San Roque','Santa Cruz','Santo Niño','Sapangdaku','Sawang Calero','Sinsin','Sirao','Suba Pasil','Sudlon I','Sudlon II','T. Padilla','Tabunan','Tagbao','Talamban','Taptap','Tejero','Tinago','Tisa','To-ong Pardo','Toong','Tuburan'],
  'Quezon City':['Alicia','Amihan','Apolonio Samson','Aurora','Baesa','Bagbag','Bagong Lipunan ng Crame','Bagong Pag-asa','Bagong Silangan','Bagumbayan','Bagumbuhay','Bahay Toro','Balingasa','Balintawak','Batasan Hills','Bayanihan','Blue Ridge A','Blue Ridge B','Botocan','Bungad','Camp Aguinaldo','Capri','Central','Claro','Commonwealth','Culiat','Damar','Damayan','Damayan Lagi','Damayang Lagi','Del Monte','Dioquino Zobel','Don Manuel','Doña Aurora','Doña Imelda','Doña Josefa','Duyan-Duyan','E. Rodriguez','East Kamias','Escopa I','Escopa II','Escopa III','Escopa IV','Fairview','Greater Lagro','Gulod','Holy Spirit','Horseshoe','Immaculate Concepcion','Kaligayahan','Kalusugan','Kamuning','Katipunan','Kaunlaran','Kristong Hari','Krus na Ligas','Laging Handa','Libis','Lourdes','Loyola Heights','Maharlika','Malaya','Manresa','Mariana','Mariblo','Marilag','Masagana','Masambong','Matandang Balara','Milagrosa','N.S. Amoranto Sr.','Nagkaisang Nayon','Nayong Kanluran','New Era','Novaliches Proper','Obrero','Old Capitol Site','Paang Bundok','Pag-ibig sa Nayon','Paligsahan','Paltok','Pansol','Paraiso','Pasong Putik Proper','Pasong Tamo','Payatas','Phil-Am','Pinagkaisahan','Pinyahan','Project 6','Quirino 2-A','Quirino 2-B','Quirino 2-C','Quirino 3-A','Ramon Magsaysay','Roxas','Sacred Heart','Saint Ignatius','Saint Peter','Salvacion','San Agustin','San Antonio','San Bartolome','San Isidro Galas','San Isidro Labrador','San Jose','San Martin de Porres','San Roque','Santa Cruz','Santa Lucia','Santa Monica','Santa Teresita','Santo Cristo','Santo Domingo','Santo Niño','Santol','Sauyo','Siena','Sikatuna Village','Silangan','Socorro','South Triangle','Tagumpay','Talayan','Talipapa','Tandang Sora','Tatalon','Teachers Village East','Teachers Village West','Ugong Norte','Unang Sigaw','UP Campus','UP Village','Vasra','Veterans Village','Villa Maria Clara','West Kamias','West Triangle','White Plains'],
  'Manila':['Barangay 1','Barangay 2','Barangay 3','Barangay 4','Barangay 5','Barangay 6','Barangay 7','Barangay 8','Barangay 9','Barangay 10','Barangay 11','Barangay 12','Barangay 13','Barangay 14','Barangay 15','Barangay 16','Barangay 17','Barangay 18','Barangay 19','Barangay 20','Barangay 21','Barangay 22','Barangay 23','Barangay 24','Barangay 25','Barangay 26','Barangay 27','Barangay 28','Barangay 29','Barangay 30','Barangay 31','Barangay 32','Barangay 33','Barangay 34','Barangay 35','Barangay 36','Barangay 37','Barangay 38','Barangay 39','Barangay 40','Barangay 41','Barangay 42','Barangay 43','Barangay 44','Barangay 45','Barangay 46','Barangay 47','Barangay 48','Barangay 49','Barangay 50','Barangay 51','Barangay 52','Barangay 53','Barangay 54','Barangay 55','Barangay 56','Barangay 57','Barangay 58','Barangay 59','Barangay 60','Barangay 61','Barangay 62','Barangay 63','Barangay 64','Barangay 65','Barangay 66','Barangay 67','Barangay 68','Barangay 69','Barangay 70','Barangay 71','Barangay 72','Barangay 73','Barangay 74','Barangay 75','Barangay 76','Barangay 77','Barangay 78','Barangay 79','Barangay 80','Barangay 81','Barangay 82','Barangay 83','Barangay 84','Barangay 85','Barangay 86','Barangay 87','Barangay 88','Barangay 89','Barangay 90','Barangay 91','Barangay 92','Barangay 93','Barangay 94','Barangay 95','Barangay 96','Barangay 97','Barangay 98','Barangay 99','Barangay 100','Barangay 101','Barangay 102','Barangay 103','Barangay 104','Barangay 105','Barangay 106','Barangay 107','Barangay 108','Barangay 109','Barangay 110','Barangay 111','Barangay 112','Barangay 113','Barangay 114','Barangay 115','Barangay 116','Barangay 117','Barangay 118','Barangay 119','Barangay 120','Barangay 121','Barangay 122','Barangay 123','Barangay 124','Barangay 125','Barangay 126','Barangay 127','Barangay 128','Barangay 129','Barangay 130','Barangay 131','Barangay 132','Barangay 133','Barangay 134','Barangay 135','Barangay 136','Barangay 137','Barangay 138','Barangay 139','Barangay 140','Barangay 141','Barangay 142','Barangay 143','Barangay 144','Barangay 145','Barangay 146','Barangay 147','Barangay 148','Barangay 149','Barangay 150','Barangay 151','Barangay 152','Barangay 153','Barangay 154','Barangay 155','Barangay 156','Barangay 157','Barangay 158','Barangay 159','Barangay 160','Barangay 161','Barangay 162','Barangay 163','Barangay 164','Barangay 165','Barangay 166','Barangay 167','Barangay 168','Barangay 169','Barangay 170','Barangay 171','Barangay 172','Barangay 173','Barangay 174','Barangay 175','Barangay 176','Barangay 177','Barangay 178','Barangay 179','Barangay 180','Barangay 181','Barangay 182','Barangay 183','Barangay 184','Barangay 185','Barangay 186','Barangay 187','Barangay 188','Barangay 189','Barangay 190','Barangay 191','Barangay 192','Barangay 193','Barangay 194','Barangay 195','Barangay 196','Barangay 197','Barangay 198','Barangay 199','Barangay 200','Barangay 201','Barangay 202','Barangay 203','Barangay 204','Barangay 205','Barangay 206','Barangay 207','Barangay 208','Barangay 209','Barangay 210','Barangay 211','Barangay 212','Barangay 213','Barangay 214','Barangay 215','Barangay 216','Barangay 217','Barangay 218','Barangay 219','Barangay 220','Barangay 221','Barangay 222','Barangay 223','Barangay 224','Barangay 225','Barangay 226','Barangay 227','Barangay 228','Barangay 229','Barangay 230','Barangay 231','Barangay 232','Barangay 233','Barangay 234','Barangay 235','Barangay 236','Barangay 237','Barangay 238','Barangay 239','Barangay 240','Barangay 241','Barangay 242','Barangay 243','Barangay 244','Barangay 245','Barangay 246','Barangay 247','Barangay 248','Barangay 249','Barangay 250','Barangay 251','Barangay 252','Barangay 253','Barangay 254','Barangay 255','Barangay 256','Barangay 257','Barangay 258','Barangay 259','Barangay 260','Barangay 261','Barangay 262','Barangay 263','Barangay 264','Barangay 265','Barangay 266','Barangay 267','Barangay 268','Barangay 269','Barangay 270','Barangay 271','Barangay 272','Barangay 273','Barangay 274','Barangay 275','Barangay 276','Barangay 277','Barangay 278','Barangay 279','Barangay 280','Barangay 281','Barangay 282','Barangay 283','Barangay 284','Barangay 285','Barangay 286','Barangay 287','Barangay 288','Barangay 289','Barangay 290','Barangay 291','Barangay 292','Barangay 293','Barangay 294','Barangay 295','Barangay 296','Barangay 297','Barangay 298','Barangay 299','Barangay 300','Barangay 301','Barangay 302','Barangay 303','Barangay 304','Barangay 305','Barangay 306','Barangay 307','Barangay 308','Barangay 309','Barangay 310','Barangay 311','Barangay 312','Barangay 313','Barangay 314','Barangay 315','Barangay 316','Barangay 317','Barangay 318','Barangay 319','Barangay 320','Barangay 321','Barangay 322','Barangay 323','Barangay 324','Barangay 325','Barangay 326','Barangay 327','Barangay 328','Barangay 329','Barangay 330','Barangay 331','Barangay 332','Barangay 333','Barangay 334','Barangay 335','Barangay 336','Barangay 337','Barangay 338','Barangay 339','Barangay 340','Barangay 341','Barangay 342','Barangay 343','Barangay 344','Barangay 345','Barangay 346','Barangay 347','Barangay 348','Barangay 349','Barangay 350','Barangay 351','Barangay 352','Barangay 353','Barangay 354','Barangay 355','Barangay 356','Barangay 357','Barangay 358','Barangay 359','Barangay 360','Barangay 361','Barangay 362','Barangay 363','Barangay 364','Barangay 365','Barangay 366','Barangay 367','Barangay 368','Barangay 369','Barangay 370','Barangay 371','Barangay 372','Barangay 373','Barangay 374','Barangay 375','Barangay 376','Barangay 377','Barangay 378','Barangay 379','Barangay 380','Barangay 381','Barangay 382','Barangay 383','Barangay 384','Barangay 385','Barangay 386','Barangay 387','Barangay 388','Barangay 389','Barangay 390','Barangay 391','Barangay 392','Barangay 393','Barangay 394','Barangay 395','Barangay 396','Barangay 397','Barangay 398','Barangay 399','Barangay 400','Barangay 401','Barangay 402','Barangay 403','Barangay 404','Barangay 405','Barangay 406','Barangay 407','Barangay 408','Barangay 409','Barangay 410','Barangay 411','Barangay 412','Barangay 413','Barangay 414','Barangay 415','Barangay 416','Barangay 417','Barangay 418','Barangay 419','Barangay 420'],
  'Makati':['Bangkal','Bel-Air','Carmona','Cembo','Comembo','Dasmariñas','East Rembo','Forbes Park','Guadalupe Nuevo','Guadalupe Viejo','Kasilawan','La Paz','Magallanes','Olympia','Palanan','Pembo','Pinagkaisahan','Pio del Pilar','Pitogo','Poblacion','Post Proper Northside','Post Proper Southside','Rizal','San Antonio','San Isidro','San Lorenzo','Santa Cruz','Singkamas','South Cembo','Tejeros','Urdaneta','Valenzuela','West Rembo'],
  'Taguig':['Bagumbayan','Bambang','Calzada','Central Bicutan','Central Signal Village','Fort Bonifacio','Hagonoy','Ibayo-Tipas','Katuparan','Ligid-Tipas','Lower Bicutan','Maharlika Village','Napindan','New Lower Bicutan','North Daang Hari','North Signal Village','Palingon','Pinagsama','San Miguel','Santa Ana','South Daang Hari','South Signal Village','Tanyag','Tuktukan','Upper Bicutan','Ususan','Wawa','Western Bicutan'],
  'Pasig':['Bagong Ilog','Bagong Katipunan','Bambang','Buting','Caniogan','Dela Paz','Kalawaan','Kapasigan','Kapitolyo','Malinao','Manggahan','Maybunga','Oranbo','Palatiw','Pinagbuhatan','Pineda','Rosario','Sagad','San Antonio','San Joaquin','San Jose','San Nicolas','Santa Cruz','Santa Lucia','Santa Rosa','Santo Tomas','Santolan','Sumilang','Ugong'],
  'Cagayan de Oro City':['Agusan','Balubal','Balulang','Barangay 1','Barangay 2','Barangay 3','Barangay 4','Barangay 5','Barangay 6','Barangay 7','Barangay 8','Barangay 9','Barangay 10','Barangay 11','Barangay 12','Barangay 13','Barangay 14','Barangay 15','Barangay 16','Barangay 17','Barangay 18','Barangay 19','Barangay 20','Barangay 21','Barangay 22','Barangay 23','Barangay 24','Barangay 25','Barangay 26','Barangay 27','Barangay 28','Barangay 29','Barangay 30','Barangay 31','Barangay 32','Barangay 33','Barangay 34','Barangay 35','Barangay 36','Barangay 37','Barangay 38','Barangay 39','Barangay 40','Bayabas','Bayanga','Besigan','Bonbon','Bugo','Bulua','Camaman-an','Canitoan','Carmen','Consolacion','Cugman','Dansolihon','F.S. Catanico','Gusa','Indahag','Iponan','Kauswagan','Lapasan','Lumbia','Macabalan','Macasandig','Mambuaya','Nazareth','Pagalungan','Pagatpat','Patag','Pigsag-an','Puerto','Puntod','San Simon','Tablon','Taglimao','Tignapoloan','Tumpagon','Tuburan','Urduja','Vilanueva'],
  'General Santos City':['Apopong','Baluan','Batomelong','Buayan','Bula','Calumpang','City Heights','Conel','Dadiangas East','Dadiangas North','Dadiangas South','Dadiangas West','Fatima','Katangawan','Labangal','Lagao','Ligaya','Mabuhay','Olympog','San Isidro','San Jose','Sinawal','Tambler','Tinagacan','Upper Labay'],
  'Iloilo City':['Arevalo','Bonifacio','Buhang','Buntatala','Calaparan','Calubihan','Camalig','City Proper','Compania','Concepcion-Montes','Dungon','Dungon A','Dungon B','Flores','Funtac','Hibao-an Norte','Hibao-an Sur','Hinactacan','Ingore','Jaro','Jalandoni Estate','Lapaz','Libertad','Loboc','Maasin','Macarthur','Mandurriao','Molo','Nabitasan','Pale Benedicto Rizal','Poblacion Molo','Progreso','Punong','Rizal Pala-pala','Sambag','San Isidro','San Jose','Santa Cruz','Santo Niño Norte','Santo Niño Sur','Santo Rosario','Seminario','Simon Ledesma','Taal','Tacas','Tagbac','Taytay Zone II','Ticud','Ungka I','Ungka II','Villa Anita'],
  'Zamboanga City':['Arena Blanco','Ayala','Baliwasan','Baluno','Boalan','Bolong','Buenavista','Bunguiao','Busay','Cabaluay','Cabatangan','Cacao','Calabasa','Calarian','Camino Nuevo','Campo Islam','Canelar','Capisan','Cawit','Culianan','Curuan','Dita','Divisoria','Dulian','Dulian Upper West','Guiwan','Kasanyangan','La Paz','Labuan','Lamisahan','Landang Gua','Landang Laum','Lanzones','Lapakan','Latuan','Licomo','Limaong','Limpapa','Lubigan','Lumayang','Lumbangan','Lunzuran','Maasin','Malagutay','Mampang','Manalipa','Mangusu','Manicahan','Mariki','Mercedes','Muti','Pamucutan','Pangapuyan','Panubigan','Pasilmanta','Pasobolong','Patalon','Putik','Quiniput','Recodo','Rio Hondo','Salaan','San Jose Cawa-Cawa','San Jose Gusu','San Roque','Sangali','Santa Barbara','Santa Catalina','Santa Maria','Santo Niño','Sibulao','Sinubung','Sinunuc','Tagasilay','Taguiti','Talabaan','Talisayan','Talon-Talon','Taluksangay','Tetuan','Tictapul','Tigbalabag','Tigtabon','Tolosa','Tugbungan','Tulungatung','Tumaga','Tumalutab','Tumitus','Victoria','Vitali','Waling-Waling','Zambowood']
};

/* ── CASCADING SELECT FUNCTIONS (shared by Step 2 and Address Modal) ─── */

function populateSelect(selectEl, items, placeholder) {
  selectEl.innerHTML = '<option value="">' + placeholder + '</option>';
  (items || []).sort().forEach(function(item) {
    var opt = document.createElement('option');
    opt.value = item; opt.textContent = item;
    selectEl.appendChild(opt);
  });
  selectEl.disabled = !items || !items.length;
}

/* ── Step 2 cascading ── */
function onRegionChange() {
  var region = document.getElementById('locRegion').value;
  var provSel = document.getElementById('locProvince');
  var citySel = document.getElementById('locCity');
  var brSel   = document.getElementById('locBarangay');
  populateSelect(provSel, PH_PROVINCES[region] || [], 'Select Province');
  populateSelect(citySel, [], 'Select City / Municipality');
  populateSelect(brSel,   [], 'Select Barangay');
  updateGeneralLocation();
}

function onProvinceChange() {
  var prov    = document.getElementById('locProvince').value;
  var citySel = document.getElementById('locCity');
  var brSel   = document.getElementById('locBarangay');
  populateSelect(citySel, PH_CITIES[prov] || [], 'Select City / Municipality');
  populateSelect(brSel,   [], 'Select Barangay');
  updateGeneralLocation();
}

function onCityChange() {
  var city  = document.getElementById('locCity').value;
  var brSel = document.getElementById('locBarangay');
  populateSelect(brSel, PH_BARANGAYS[city] || [], 'Select Barangay');
  updateGeneralLocation();
}

function onBarangayChange() { updateGeneralLocation(); }

function updateGeneralLocation() {
  var region = document.getElementById('locRegion').options[document.getElementById('locRegion').selectedIndex]?.text || '';
  var prov   = document.getElementById('locProvince').value;
  var city   = document.getElementById('locCity').value;
  var brgy   = document.getElementById('locBarangay').value;
  var parts  = [region.split('–')[1]?.trim() || region, prov, city, brgy].filter(Boolean);
  document.getElementById('generalLocation').value = parts.join(' / ');
  markDirty();
}

/* ── Address Modal cascading ── */
function addrOnRegion() {
  var region  = document.getElementById('addrRegion').value;
  var provSel = document.getElementById('addrProvince');
  var citySel = document.getElementById('addrCity');
  var brSel   = document.getElementById('addrBarangay');
  populateSelect(provSel, PH_PROVINCES[region] || [], 'Select Province');
  populateSelect(citySel, [], 'Select City / Municipality');
  populateSelect(brSel,   [], 'Select Barangay');
}

function addrOnProvince() {
  var prov    = document.getElementById('addrProvince').value;
  var citySel = document.getElementById('addrCity');
  var brSel   = document.getElementById('addrBarangay');
  populateSelect(citySel, PH_CITIES[prov] || [], 'Select City / Municipality');
  populateSelect(brSel,   [], 'Select Barangay');
}

function addrOnCity() {
  var city  = document.getElementById('addrCity').value;
  var brSel = document.getElementById('addrBarangay');
  populateSelect(brSel, PH_BARANGAYS[city] || [], 'Select Barangay');
  // Geocode the city on the map
  var prov = document.getElementById('addrProvince').value;
  geocodeAddress(city + ', ' + prov);
}

function addrOnBarangay() {
  var brgy = document.getElementById('addrBarangay').value;
  var city = document.getElementById('addrCity').value;
  var prov = document.getElementById('addrProvince').value;
  if (brgy) geocodeAddress(brgy + ', ' + city + ', ' + prov);
}

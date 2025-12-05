/**
 * Bus stops coordinates for Gran Canaria
 * Each stop has coordinates for both directions (ida/vuelta)
 * Coordinates are in [lat, lng] format
 * 
 * Sources:
 * - OpenStreetMap
 * - Google Maps
 * - guaguas.com
 * - guaguasglobal.com
 */

export const PARADAS = {
  // ============ TERMINALES E INTERCAMBIADORES ============
  teatro: { ida: [28.1094, -15.4167], vuelta: [28.1094, -15.4167] },
  puerto: { ida: [28.1453, -15.4310], vuelta: [28.1453, -15.4310] },
  santaCatalina: { ida: [28.1417, -15.4325], vuelta: [28.1417, -15.4325] },
  hoyaDeLaPlata: { ida: [28.0702, -15.4187], vuelta: [28.0702, -15.4187] },
  auditorio: { ida: [28.1358, -15.4465], vuelta: [28.1358, -15.4465] },
  hospitalNegrin: { ida: [28.1268, -15.4540], vuelta: [28.1268, -15.4540] },
  campusULPGC: { ida: [28.0702, -15.4533], vuelta: [28.0702, -15.4533] },
  estacionSanTelmo: { ida: [28.1042, -15.4178], vuelta: [28.1042, -15.4178] },
  
  // ============ ZONA VEGUETA / TRIANA ============
  guiniguada: { ida: [28.1019, -15.4142], vuelta: [28.1021, -15.4140] },
  sanTelmo: { ida: [28.1047, -15.4172], vuelta: [28.1049, -15.4170] },
  triana: { ida: [28.1069, -15.4188], vuelta: [28.1071, -15.4186] },
  vegueta: { ida: [28.0997, -15.4153], vuelta: [28.0999, -15.4151] },
  mercadoVegueta: { ida: [28.0989, -15.4141], vuelta: [28.0991, -15.4139] },
  sanBernardo: { ida: [28.1007, -15.4163], vuelta: [28.1009, -15.4161] },
  catedral: { ida: [28.0998, -15.4147], vuelta: [28.1000, -15.4145] },
  peregrina: { ida: [28.1012, -15.4158], vuelta: [28.1014, -15.4156] },
  plazaHurtado: { ida: [28.1055, -15.4180], vuelta: [28.1057, -15.4178] },
  
  // ============ EJE LEÓN Y CASTILLO ============
  usosMultiples: { ida: [28.1090, -15.4217], vuelta: [28.1092, -15.4215] },
  leonCastillo7: { ida: [28.1100, -15.4235], vuelta: [28.1102, -15.4233] },
  leonCastillo22: { ida: [28.1115, -15.4256], vuelta: [28.1117, -15.4254] },
  leonCastillo35: { ida: [28.1128, -15.4270], vuelta: [28.1130, -15.4268] },
  leonCastillo50: { ida: [28.1140, -15.4285], vuelta: [28.1142, -15.4283] },
  leonCastillo67: { ida: [28.1155, -15.4302], vuelta: [28.1157, -15.4300] },
  oficinasMunicipales: { ida: [28.1178, -15.4323], vuelta: [28.1180, -15.4321] },
  torreLasPalmas: { ida: [28.1210, -15.4358], vuelta: [28.1212, -15.4356] },
  rafaelCabrera: { ida: [28.1195, -15.4340], vuelta: [28.1197, -15.4338] },
  
  // ============ EJE MESA Y LÓPEZ / TOMÁS MORALES ============
  mesaYLopez: { ida: [28.1318, -15.4420], vuelta: [28.1320, -15.4418] },
  galicia: { ida: [28.1365, -15.4380], vuelta: [28.1367, -15.4378] },
  nicolasEstevañez: { ida: [28.1285, -15.4395], vuelta: [28.1287, -15.4393] },
  nestor: { ida: [28.1300, -15.4405], vuelta: [28.1302, -15.4403] },
  tomasMorales: { ida: [28.1125, -15.4280], vuelta: [28.1127, -15.4278] },
  tomasMorales35: { ida: [28.1145, -15.4310], vuelta: [28.1147, -15.4308] },
  tomasMorales60: { ida: [28.1165, -15.4345], vuelta: [28.1167, -15.4343] },
  pioXII: { ida: [28.1220, -15.4385], vuelta: [28.1222, -15.4383] },
  
  // ============ ZONA PUERTO / SANTA CATALINA / CANTERAS ============
  canteras: { ida: [28.1408, -15.4360], vuelta: [28.1410, -15.4358] },
  lasArenas: { ida: [28.1420, -15.4375], vuelta: [28.1422, -15.4373] },
  alcaravaneras: { ida: [28.1312, -15.4320], vuelta: [28.1314, -15.4318] },
  parqueSantaCatalina: { ida: [28.1405, -15.4330], vuelta: [28.1407, -15.4328] },
  ciudadJardin: { ida: [28.1260, -15.4290], vuelta: [28.1262, -15.4288] },
  julioVerne: { ida: [28.1380, -15.4340], vuelta: [28.1382, -15.4338] },
  lopezSocas: { ida: [28.1430, -15.4350], vuelta: [28.1432, -15.4348] },
  ferreras: { ida: [28.1395, -15.4320], vuelta: [28.1397, -15.4318] },
  
  // ============ ESTADIO INSULAR / MERCADO CENTRAL ============
  estadioInsular: { ida: [28.1190, -15.4400], vuelta: [28.1192, -15.4398] },
  mercadoCentral: { ida: [28.1330, -15.4365], vuelta: [28.1332, -15.4363] },
  seccion3: { ida: [28.1340, -15.4375], vuelta: [28.1342, -15.4373] },
  marivaGomez: { ida: [28.1350, -15.4385], vuelta: [28.1352, -15.4383] },
  
  // ============ LA FERIA / ESCALERITAS / SCHAMANN ============
  donZoilo: { ida: [28.1040, -15.4260], vuelta: [28.1042, -15.4258] },
  altavista: { ida: [28.1075, -15.4320], vuelta: [28.1077, -15.4318] },
  lasChumberas: { ida: [28.1090, -15.4380], vuelta: [28.1092, -15.4378] },
  laFeria: { ida: [28.1125, -15.4450], vuelta: [28.1127, -15.4448] },
  feriaNumero5: { ida: [28.1130, -15.4460], vuelta: [28.1132, -15.4458] },
  escaleritas: { ida: [28.1150, -15.4415], vuelta: [28.1152, -15.4413] },
  escaleritasMercado: { ida: [28.1155, -15.4425], vuelta: [28.1157, -15.4423] },
  schamann: { ida: [28.1115, -15.4385], vuelta: [28.1117, -15.4383] },
  schamannPlaza: { ida: [28.1118, -15.4390], vuelta: [28.1120, -15.4388] },
  laMinilla: { ida: [28.1330, -15.4420], vuelta: [28.1332, -15.4418] },
  elPilar: { ida: [28.1165, -15.4495], vuelta: [28.1167, -15.4493] },
  
  // ============ SIETE PALMAS / TAMARACEITE ============
  sietePalmas: { ida: [28.1205, -15.4580], vuelta: [28.1207, -15.4578] },
  sietePalmasCC: { ida: [28.1210, -15.4590], vuelta: [28.1212, -15.4588] },
  juanCarlosI: { ida: [28.1250, -15.4515], vuelta: [28.1252, -15.4513] },
  tamaraceite: { ida: [28.1380, -15.4720], vuelta: [28.1382, -15.4718] },
  tenoya: { ida: [28.1420, -15.4780], vuelta: [28.1422, -15.4778] },
  arucas: { ida: [28.1195, -15.5235], vuelta: [28.1195, -15.5235] },
  teror: { ida: [28.0590, -15.5475], vuelta: [28.0590, -15.5475] },
  
  // ============ CONO SUR ============
  sanJose: { ida: [28.0850, -15.4175], vuelta: [28.0852, -15.4173] },
  paseoSanJose: { ida: [28.0860, -15.4170], vuelta: [28.0862, -15.4168] },
  ciudadJusticia: { ida: [28.0780, -15.4172], vuelta: [28.0782, -15.4170] },
  blasCabrera: { ida: [28.0730, -15.4182], vuelta: [28.0732, -15.4180] },
  elLasso: { ida: [28.0805, -15.4188], vuelta: [28.0807, -15.4186] },
  tresPalmas: { ida: [28.0615, -15.4235], vuelta: [28.0617, -15.4233] },
  pedroHidalgo: { ida: [28.0680, -15.4210], vuelta: [28.0682, -15.4208] },
  casablanca: { ida: [28.0745, -15.4195], vuelta: [28.0747, -15.4193] },
  millerBajo: { ida: [28.0895, -15.4145], vuelta: [28.0897, -15.4143] },
  millerAlto: { ida: [28.0910, -15.4155], vuelta: [28.0912, -15.4153] },
  dominguezAlfonso: { ida: [28.0820, -15.4165], vuelta: [28.0822, -15.4163] },
  jinamar: { ida: [28.0274, -15.4142], vuelta: [28.0276, -15.4140] },
  
  // ============ GUANARTEME / ISLETA ============
  guanarteme: { ida: [28.1340, -15.4450], vuelta: [28.1342, -15.4448] },
  guanartemeIglesia: { ida: [28.1345, -15.4455], vuelta: [28.1347, -15.4453] },
  baseNaval: { ida: [28.1475, -15.4325], vuelta: [28.1477, -15.4323] },
  isleta: { ida: [28.1520, -15.4295], vuelta: [28.1522, -15.4293] },
  confital: { ida: [28.1485, -15.4445], vuelta: [28.1487, -15.4443] },
  
  // ============ TAFIRA / CAMPUS ============
  tafiraAlta: { ida: [28.0770, -15.4520], vuelta: [28.0772, -15.4518] },
  tafiraBaja: { ida: [28.0830, -15.4470], vuelta: [28.0832, -15.4468] },
  jardinesJamar: { ida: [28.0690, -15.4545], vuelta: [28.0692, -15.4543] },
  
  // ============ OTRAS ZONAS URBANAS ============
  lomoApolinario: { ida: [28.0982, -15.4280], vuelta: [28.0984, -15.4278] },
  cruzDePiedra: { ida: [28.0945, -15.4230], vuelta: [28.0947, -15.4228] },
  lasRehoyas: { ida: [28.0935, -15.4310], vuelta: [28.0937, -15.4308] },
  elBatanMiller: { ida: [28.0920, -15.4265], vuelta: [28.0922, -15.4263] },
  juanRamon: { ida: [28.0905, -15.4215], vuelta: [28.0907, -15.4213] },
  isletaDeportiva: { ida: [28.1540, -15.4280], vuelta: [28.1542, -15.4278] },
  darsena: { ida: [28.1510, -15.4260], vuelta: [28.1512, -15.4258] },
  laPaterna: { ida: [28.1080, -15.4340], vuelta: [28.1082, -15.4338] },
  rehoyas: { ida: [28.0940, -15.4305], vuelta: [28.0942, -15.4303] },
  lomoBlanco: { ida: [28.1020, -15.4350], vuelta: [28.1022, -15.4348] },
  sanFrancisco: { ida: [28.1035, -15.4380], vuelta: [28.1037, -15.4378] },
  sanCristobal: { ida: [28.0830, -15.4125], vuelta: [28.0832, -15.4123] },
  lasColoradas: { ida: [28.0760, -15.4110], vuelta: [28.0762, -15.4108] },
  elSecadero: { ida: [28.0880, -15.4290], vuelta: [28.0882, -15.4288] },
  elBatan: { ida: [28.0925, -15.4270], vuelta: [28.0927, -15.4268] },
  sanJuan: { ida: [28.0975, -15.4125], vuelta: [28.0977, -15.4123] },
  hospitalJuanCarlosI: { ida: [28.1015, -15.4395], vuelta: [28.1017, -15.4393] },
  lomoDeLaCruz: { ida: [28.0880, -15.4320], vuelta: [28.0882, -15.4318] },
  carreteraMata: { ida: [28.0950, -15.4340], vuelta: [28.0952, -15.4338] },
  carreteraGeneralNorte: { ida: [28.1060, -15.4355], vuelta: [28.1062, -15.4353] },
  laBallena: { ida: [28.1145, -15.4470], vuelta: [28.1147, -15.4468] },
  lasTorres: { ida: [28.1135, -15.4435], vuelta: [28.1137, -15.4433] },
  sanLorenzo: { ida: [28.1320, -15.4680], vuelta: [28.1322, -15.4678] },
  almatriche: { ida: [28.1285, -15.4650], vuelta: [28.1287, -15.4648] },
  lomoLosFrailes: { ida: [28.1350, -15.4700], vuelta: [28.1352, -15.4698] },
  islaPerdida: { ida: [28.1410, -15.4750], vuelta: [28.1412, -15.4748] },
  piletas: { ida: [28.1395, -15.4735], vuelta: [28.1397, -15.4733] },
  laSuerte: { ida: [28.1365, -15.4710], vuelta: [28.1367, -15.4708] },
  hoyaAndrea: { ida: [28.1230, -15.4620], vuelta: [28.1232, -15.4618] },
  zarate: { ida: [28.0580, -15.4260], vuelta: [28.0582, -15.4258] },
  ciudadDeportivaGC: { ida: [28.0740, -15.4155], vuelta: [28.0742, -15.4153] },
  chile: { ida: [28.1140, -15.4445], vuelta: [28.1142, -15.4443] },
  paseoSanAntonio: { ida: [28.1135, -15.4400], vuelta: [28.1137, -15.4398] },
  sebadal: { ida: [28.1480, -15.4380], vuelta: [28.1482, -15.4378] },
  zonaPortuaria: { ida: [28.1500, -15.4340], vuelta: [28.1502, -15.4338] },
  
  // ============ GLOBAL - INTERURBANAS ============
  telde: { ida: [27.9941, -15.4166], vuelta: [27.9941, -15.4166] },
  teldeIntercambiador: { ida: [27.9945, -15.4170], vuelta: [27.9945, -15.4170] },
  aeropuerto: { ida: [27.9319, -15.3866], vuelta: [27.9319, -15.3866] },
  vecindario: { ida: [27.8414, -15.4489], vuelta: [27.8414, -15.4489] },
  sanAgustin: { ida: [27.7750, -15.5480], vuelta: [27.7750, -15.5480] },
  playaIngles: { ida: [27.7580, -15.5720], vuelta: [27.7580, -15.5720] },
  maspalomas: { ida: [27.7609, -15.5865], vuelta: [27.7609, -15.5865] },
  aguimes: { ida: [27.9050, -15.4445], vuelta: [27.9050, -15.4445] },
  ingenio: { ida: [27.9170, -15.4360], vuelta: [27.9170, -15.4360] },
  cruce_de_arinaga: { ida: [27.8680, -15.4100], vuelta: [27.8680, -15.4100] },
  arinaga: { ida: [27.8590, -15.3950], vuelta: [27.8590, -15.3950] },
  carrizal: { ida: [27.9030, -15.4290], vuelta: [27.9030, -15.4290] },
  melenara: { ida: [27.9885, -15.3760], vuelta: [27.9885, -15.3760] },
  salinetas: { ida: [27.9745, -15.3885], vuelta: [27.9745, -15.3885] },
  
  // ============ GLOBAL - SUR Y OESTE ============
  puertoMogan: { ida: [27.8155, -15.7635], vuelta: [27.8155, -15.7635] },
  arguineguin: { ida: [27.7610, -15.6815], vuelta: [27.7610, -15.6815] },
  puertoRico: { ida: [27.7870, -15.7085], vuelta: [27.7870, -15.7085] },
  taurito: { ida: [27.8020, -15.7380], vuelta: [27.8020, -15.7380] },
  tableroMaspalomas: { ida: [27.7690, -15.5740], vuelta: [27.7690, -15.5740] },
  sanFernando: { ida: [27.7650, -15.5645], vuelta: [27.7650, -15.5645] },
  bahiaFeliz: { ida: [27.7940, -15.5120], vuelta: [27.7940, -15.5120] },
  playaCura: { ida: [27.7915, -15.7150], vuelta: [27.7915, -15.7150] },
  palmitosPark: { ida: [27.8060, -15.5780], vuelta: [27.8060, -15.5780] },
  aldeaSanNicolas: { ida: [27.9845, -15.7795], vuelta: [27.9845, -15.7795] },
  doctoral: { ida: [27.8310, -15.4650], vuelta: [27.8310, -15.4650] },
  playaArinaga: { ida: [27.8520, -15.3865], vuelta: [27.8520, -15.3865] },
  castilloRomeral: { ida: [27.8175, -15.4760], vuelta: [27.8175, -15.4760] },
  sardina_sur: { ida: [27.8715, -15.4375], vuelta: [27.8715, -15.4375] },
  
  // ============ GLOBAL - NORTE ============
  galdar: { ida: [28.1465, -15.6495], vuelta: [28.1465, -15.6495] },
  guia: { ida: [28.1395, -15.6335], vuelta: [28.1395, -15.6335] },
  agaete: { ida: [28.1015, -15.6985], vuelta: [28.1015, -15.6985] },
  puertoNieves: { ida: [28.0985, -15.7025], vuelta: [28.0985, -15.7025] },
  moya: { ida: [28.1095, -15.5870], vuelta: [28.1095, -15.5870] },
  firgas: { ida: [28.1090, -15.5615], vuelta: [28.1090, -15.5615] },
  bananeros: { ida: [28.1355, -15.5275], vuelta: [28.1355, -15.5275] },
  cardones: { ida: [28.1280, -15.5180], vuelta: [28.1280, -15.5180] },
  
  // ============ GLOBAL - CENTRO ============
  santaBrigida: { ida: [28.0340, -15.5005], vuelta: [28.0340, -15.5005] },
  sanMateo: { ida: [28.0135, -15.5320], vuelta: [28.0135, -15.5320] },
  tejeda: { ida: [27.9940, -15.6140], vuelta: [27.9940, -15.6140] },
  artenara: { ida: [28.0205, -15.6445], vuelta: [28.0205, -15.6445] },
  valsequillo: { ida: [27.9870, -15.4955], vuelta: [27.9870, -15.4955] },
  tenteniguada: { ida: [27.9675, -15.5125], vuelta: [27.9675, -15.5125] },
  
  // ============ GLOBAL - TELDE/JINAMAR ============
  valleJinamar: { ida: [28.0275, -15.4205], vuelta: [28.0275, -15.4205] },
  eucaliptos2: { ida: [28.0235, -15.4165], vuelta: [28.0235, -15.4165] },
  ramblasJinamar: { ida: [28.0210, -15.4185], vuelta: [28.0210, -15.4185] },
  laHerradura: { ida: [27.9870, -15.4305], vuelta: [27.9870, -15.4305] },
  lasRemudas: { ida: [28.0015, -15.4080], vuelta: [28.0015, -15.4080] },
  montaneta: { ida: [28.0105, -15.4050], vuelta: [28.0105, -15.4050] },
  vialCosteroTelde: { ida: [27.9765, -15.3895], vuelta: [27.9765, -15.3895] },
  playaBurrero: { ida: [27.9620, -15.3710], vuelta: [27.9620, -15.3710] },
  
  // ============ GLOBAL - UNIVERSITARIO ============
  cementerioSanLazaro: { ida: [28.0815, -15.4505], vuelta: [28.0815, -15.4505] },
  fondillo: { ida: [28.0445, -15.4745], vuelta: [28.0445, -15.4745] },
  ciudadCampoAlto: { ida: [28.0520, -15.4810], vuelta: [28.0520, -15.4810] },
  lomoBlancoCasas: { ida: [28.0985, -15.4410], vuelta: [28.0985, -15.4410] },
  
  // ============ GLOBAL - TAMARACEITE/NORTE LP ============
  tenoyaAlta: { ida: [28.1455, -15.4820], vuelta: [28.1455, -15.4820] },
  sanJoseAlamo: { ida: [28.1030, -15.5085], vuelta: [28.1030, -15.5085] },
  lanzarote: { ida: [28.0810, -15.5245], vuelta: [28.0810, -15.5245] },
  lasMesas: { ida: [28.1045, -15.4895], vuelta: [28.1045, -15.4895] },
  elToscon: { ida: [28.0725, -15.5165], vuelta: [28.0725, -15.5165] },
  caboVerde: { ida: [28.1220, -15.5785], vuelta: [28.1220, -15.5785] },
  losDragos: { ida: [28.1125, -15.5810], vuelta: [28.1125, -15.5810] },
  fontanales: { ida: [28.0785, -15.5990], vuelta: [28.0785, -15.5990] },
  
  // ============ GLOBAL - GÁLDAR (400s) ============
  sardinaNorte: { ida: [28.1595, -15.6895], vuelta: [28.1595, -15.6895] },
  barrial: { ida: [28.1545, -15.6750], vuelta: [28.1545, -15.6750] },
  cumbrecillasFaro: { ida: [28.1560, -15.6820], vuelta: [28.1560, -15.6820] },
  dosRoques: { ida: [28.1485, -15.6580], vuelta: [28.1485, -15.6580] },
  laFurnia: { ida: [28.1510, -15.6620], vuelta: [28.1510, -15.6620] },
  puntaGaldar: { ida: [28.1535, -15.6685], vuelta: [28.1535, -15.6685] },
  elRoque: { ida: [28.1520, -15.6640], vuelta: [28.1520, -15.6640] },
  fagajesto: { ida: [28.0955, -15.6320], vuelta: [28.0955, -15.6320] },
  montanaAlta: { ida: [28.1255, -15.6125], vuelta: [28.1255, -15.6125] },
  marmolejos: { ida: [28.1185, -15.6045], vuelta: [28.1185, -15.6045] },
  elPalmital: { ida: [28.1145, -15.5925], vuelta: [28.1145, -15.5925] },
  cruzPagador: { ida: [28.1295, -15.6215], vuelta: [28.1295, -15.6215] },
  
  // ============ GLOBAL - ARUCAS (250s) ============
  santidad: { ida: [28.1245, -15.5355], vuelta: [28.1245, -15.5355] },
  laGoleta: { ida: [28.1165, -15.5485], vuelta: [28.1165, -15.5485] },
  cruzFirgas: { ida: [28.1125, -15.5555], vuelta: [28.1125, -15.5555] },
  altabacales: { ida: [28.1315, -15.5195], vuelta: [28.1315, -15.5195] },
  trasmontana: { ida: [28.1275, -15.5135], vuelta: [28.1275, -15.5135] },
  trapiche: { ida: [28.1235, -15.5285], vuelta: [28.1235, -15.5285] },
  sanFelipe: { ida: [28.1385, -15.5085], vuelta: [28.1385, -15.5085] },
  
  // ============ GLOBAL - MONTAÑA ============
  sanBartolomeTirajana: { ida: [27.9235, -15.5725], vuelta: [27.9235, -15.5725] },
  roqueNublo: { ida: [27.9680, -15.6115], vuelta: [27.9680, -15.6115] },
  ariñez: { ida: [28.0025, -15.5485], vuelta: [28.0025, -15.5485] },
  lasLagunetas: { ida: [27.9945, -15.5395], vuelta: [27.9945, -15.5395] },
  cuevaGrande: { ida: [27.9875, -15.5275], vuelta: [27.9875, -15.5275] },
  lomoCarbonero: { ida: [28.0235, -15.5125], vuelta: [28.0235, -15.5125] },
  pinoSanto: { ida: [28.0395, -15.4895], vuelta: [28.0395, -15.4895] },
  losGiles: { ida: [28.0655, -15.4685], vuelta: [28.0655, -15.4685] },
  calzada: { ida: [28.0475, -15.4855], vuelta: [28.0475, -15.4855] },
  
  // ============ GLOBAL - ADICIONALES ============
  univFdoPessoa: { ida: [28.0635, -15.4565], vuelta: [28.0635, -15.4565] },
  caeCalero: { ida: [27.9725, -15.4285], vuelta: [27.9725, -15.4285] },
  caserones: { ida: [27.9395, -15.4985], vuelta: [27.9395, -15.4985] },
  laMediania: { ida: [27.9155, -15.4725], vuelta: [27.9155, -15.4725] },
  montanaTierras: { ida: [27.9015, -15.4545], vuelta: [27.9015, -15.4545] },
  valleAgaete: { ida: [28.0875, -15.6925], vuelta: [28.0875, -15.6925] },
  playaTasarte: { ida: [27.8745, -15.7485], vuelta: [27.8745, -15.7485] },
  moganPueblo: { ida: [27.8855, -15.7285], vuelta: [27.8855, -15.7285] },
  valleseco: { ida: [28.0535, -15.5745], vuelta: [28.0535, -15.5745] },
  madreDelAgua: { ida: [28.0675, -15.5625], vuelta: [28.0675, -15.5625] },
  cuevaCorrales: { ida: [28.0725, -15.5565], vuelta: [28.0725, -15.5565] },
  barrancoPequeno: { ida: [28.0615, -15.5685], vuelta: [28.0615, -15.5685] },
  valsendero: { ida: [28.0595, -15.5815], vuelta: [28.0595, -15.5815] },
  laLaguna: { ida: [28.0485, -15.5645], vuelta: [28.0485, -15.5645] },
  elZumacal: { ida: [28.0775, -15.5485], vuelta: [28.0775, -15.5485] },
  barranco: { ida: [27.9175, -15.5455], vuelta: [27.9175, -15.5455] },
  sardinaDelSur: { ida: [27.8915, -15.4525], vuelta: [27.8915, -15.4525] },
  losMolinillos: { ida: [27.9095, -15.4065], vuelta: [27.9095, -15.4065] },
  sanRoque: { ida: [28.0015, -15.4545], vuelta: [28.0015, -15.4545] },
  casablancaFirgas: { ida: [28.0865, -15.5255], vuelta: [28.0865, -15.5255] },
  cercadoEspino: { ida: [27.8125, -15.6215], vuelta: [27.8125, -15.6215] },
  elFondillo: { ida: [28.0315, -15.5075], vuelta: [28.0315, -15.5075] },
  sanLorenzoGlobal: { ida: [28.0855, -15.4685], vuelta: [28.0855, -15.4685] },
  sanAntonio: { ida: [28.1135, -15.4410], vuelta: [28.1137, -15.4408] },
  lasColoradasPlaya: { ida: [28.0745, -15.4095], vuelta: [28.0747, -15.4093] },
};

/**
 * Get stop coordinates by name and direction
 * @param {string} parada - Stop name
 * @param {string} sentido - Direction ('ida' or 'vuelta')
 * @returns {[number, number]} Coordinates [lat, lng]
 */
export const getParada = (parada, sentido) => {
  if (!PARADAS[parada]) {
    console.warn(`Parada no encontrada: ${parada}`);
    return [28.1, -15.42]; // Default coordinate
  }
  return sentido === 'ida' ? PARADAS[parada].ida : PARADAS[parada].vuelta;
};

export default PARADAS;

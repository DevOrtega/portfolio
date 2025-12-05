/**
 * @file routes.js
 * @description Bus route definitions for Gran Canaria
 * Data sourced from https://www.guaguas.com and http://globalsu.net
 * Note: No public GTFS feed available for Gran Canaria
 */
import { getParada } from './paradas.js';

/**
 * Get all bus routes with coordinates
 * @returns {Array} Array of route objects
 */
export const getRoutes = () => {
  const routes = [
    // ========== GUAGUAS MUNICIPALES (Urban - Yellow) ==========
    
    // Línea 1: Teatro - Puerto (por León y Castillo)
    { 
      line: '1', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Puerto', 
      stopsIda: ['San Telmo', 'Usos Múltiples', 'León y Castillo 22', 'León y Castillo 50', 'Oficinas Municipales', 'Torre Las Palmas', 'Mesa y López', 'Galicia', 'Santa Catalina'],
      stopsVuelta: ['Parque Santa Catalina', 'Galicia', 'Mesa y López', 'Rafael Cabrera', 'Oficinas Municipales', 'León y Castillo 67', 'León y Castillo 35', 'León y Castillo 7', 'Triana', 'San Telmo'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('usosMultiples', 'ida'),
        getParada('leonCastillo7', 'ida'),
        getParada('leonCastillo22', 'ida'),
        getParada('leonCastillo35', 'ida'),
        getParada('leonCastillo50', 'ida'),
        getParada('leonCastillo67', 'ida'),
        getParada('oficinasMunicipales', 'ida'),
        getParada('rafaelCabrera', 'ida'),
        getParada('torreLasPalmas', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('galicia', 'ida'),
        getParada('santaCatalina', 'ida'),
        getParada('puerto', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('puerto', 'vuelta'),
        getParada('parqueSantaCatalina', 'vuelta'),
        getParada('galicia', 'vuelta'),
        getParada('mesaYLopez', 'vuelta'),
        getParada('rafaelCabrera', 'vuelta'),
        getParada('torreLasPalmas', 'vuelta'),
        getParada('oficinasMunicipales', 'vuelta'),
        getParada('leonCastillo67', 'vuelta'),
        getParada('leonCastillo50', 'vuelta'),
        getParada('leonCastillo35', 'vuelta'),
        getParada('leonCastillo22', 'vuelta'),
        getParada('leonCastillo7', 'vuelta'),
        getParada('usosMultiples', 'vuelta'),
        getParada('triana', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea 2: Guiniguada - Puerto (por Tomás Morales)
    { 
      line: '2', type: 'urban', company: 'municipales', origin: 'Guiniguada', destination: 'Puerto', 
      stopsIda: ['San Bernardo', 'Peregrina', 'Triana', 'Tomás Morales', 'Tomás Morales 35', 'Pío XII', 'Estadio Insular', 'Mercado Central', 'Sección 3', 'Galicia', 'Santa Catalina'],
      stopsVuelta: ['Parque Santa Catalina', 'Galicia', 'Mercado Central', 'Estadio Insular', 'Pío XII', 'Tomás Morales 60', 'Tomás Morales', 'Plaza Hurtado', 'San Telmo', 'Catedral'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('guiniguada', 'ida'),
        getParada('sanBernardo', 'ida'),
        getParada('peregrina', 'ida'),
        getParada('triana', 'ida'),
        getParada('tomasMorales', 'ida'),
        getParada('tomasMorales35', 'ida'),
        getParada('pioXII', 'ida'),
        getParada('estadioInsular', 'ida'),
        getParada('mercadoCentral', 'ida'),
        getParada('seccion3', 'ida'),
        getParada('galicia', 'ida'),
        getParada('santaCatalina', 'ida'),
        getParada('puerto', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('puerto', 'vuelta'),
        getParada('parqueSantaCatalina', 'vuelta'),
        getParada('galicia', 'vuelta'),
        getParada('mercadoCentral', 'vuelta'),
        getParada('estadioInsular', 'vuelta'),
        getParada('pioXII', 'vuelta'),
        getParada('tomasMorales60', 'vuelta'),
        getParada('tomasMorales35', 'vuelta'),
        getParada('tomasMorales', 'vuelta'),
        getParada('plazaHurtado', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('catedral', 'vuelta'),
        getParada('guiniguada', 'vuelta')
      ]
    },
    
    // Línea 11: Teatro - Hospital Dr. Negrín (por La Feria)
    { 
      line: '11', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Hospital Dr. Negrín', 
      stopsIda: ['San Telmo', 'Don Zoilo', 'Altavista', 'Las Chumberas', 'Schamann', 'Escaleritas', 'La Feria', 'El Pilar', 'Siete Palmas'],
      stopsVuelta: ['Siete Palmas CC', 'El Pilar', 'La Feria Nº5', 'Escaleritas Mercado', 'Schamann Plaza', 'Las Chumberas', 'Altavista', 'Don Zoilo', 'Triana'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('donZoilo', 'ida'),
        getParada('altavista', 'ida'),
        getParada('lasChumberas', 'ida'),
        getParada('schamann', 'ida'),
        getParada('escaleritas', 'ida'),
        getParada('laFeria', 'ida'),
        getParada('elPilar', 'ida'),
        getParada('sietePalmas', 'ida'),
        getParada('hospitalNegrin', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('hospitalNegrin', 'vuelta'),
        getParada('sietePalmasCC', 'vuelta'),
        getParada('elPilar', 'vuelta'),
        getParada('feriaNumero5', 'vuelta'),
        getParada('escaleritasMercado', 'vuelta'),
        getParada('schamannPlaza', 'vuelta'),
        getParada('lasChumberas', 'vuelta'),
        getParada('altavista', 'vuelta'),
        getParada('donZoilo', 'vuelta'),
        getParada('triana', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea 12: Puerto - Hoya de La Plata (Línea troncal Cono Sur)
    { 
      line: '12', type: 'urban', company: 'municipales', origin: 'Puerto', destination: 'Hoya de La Plata', 
      stopsIda: ['Santa Catalina', 'Galicia', 'Mesa y López', 'Torre Las Palmas', 'Oficinas Municipales', 'Ciudad Jardín', 'Paseo San José', 'Domínguez Alfonso', 'Ciudad Justicia', 'Blas Cabrera'],
      stopsVuelta: ['Blas Cabrera', 'Ciudad Justicia', 'Domínguez Alfonso', 'San José', 'Ciudad Jardín', 'Alcaravaneras', 'Oficinas Municipales', 'Torre Las Palmas', 'Mesa y López', 'Galicia', 'Parque Santa Catalina'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('puerto', 'ida'),
        getParada('santaCatalina', 'ida'),
        getParada('galicia', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('torreLasPalmas', 'ida'),
        getParada('oficinasMunicipales', 'ida'),
        getParada('ciudadJardin', 'ida'),
        getParada('paseoSanJose', 'ida'),
        getParada('dominguezAlfonso', 'ida'),
        getParada('ciudadJusticia', 'ida'),
        getParada('blasCabrera', 'ida'),
        getParada('hoyaDeLaPlata', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('hoyaDeLaPlata', 'vuelta'),
        getParada('blasCabrera', 'vuelta'),
        getParada('ciudadJusticia', 'vuelta'),
        getParada('dominguezAlfonso', 'vuelta'),
        getParada('sanJose', 'vuelta'),
        getParada('ciudadJardin', 'vuelta'),
        getParada('alcaravaneras', 'vuelta'),
        getParada('oficinasMunicipales', 'vuelta'),
        getParada('torreLasPalmas', 'vuelta'),
        getParada('mesaYLopez', 'vuelta'),
        getParada('galicia', 'vuelta'),
        getParada('parqueSantaCatalina', 'vuelta'),
        getParada('puerto', 'vuelta')
      ]
    },
    
    // Línea 13: Mercado de Vegueta - Tres Palmas (Línea troncal Cono Sur)
    { 
      line: '13', type: 'urban', company: 'municipales', origin: 'Mercado de Vegueta', destination: 'Tres Palmas', 
      stopsIda: ['Vegueta', 'Catedral', 'Miller Bajo', 'Miller Alto', 'San José', 'El Lasso', 'Casablanca', 'Blas Cabrera', 'Hoya de La Plata', 'Pedro Hidalgo'],
      stopsVuelta: ['Pedro Hidalgo', 'Hoya de La Plata', 'Blas Cabrera', 'Casablanca', 'El Lasso', 'Paseo San José', 'Miller Alto', 'Miller Bajo', 'San Bernardo', 'Mercado Vegueta'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('mercadoVegueta', 'ida'),
        getParada('vegueta', 'ida'),
        getParada('catedral', 'ida'),
        getParada('millerBajo', 'ida'),
        getParada('millerAlto', 'ida'),
        getParada('sanJose', 'ida'),
        getParada('elLasso', 'ida'),
        getParada('casablanca', 'ida'),
        getParada('blasCabrera', 'ida'),
        getParada('hoyaDeLaPlata', 'ida'),
        getParada('pedroHidalgo', 'ida'),
        getParada('tresPalmas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tresPalmas', 'vuelta'),
        getParada('pedroHidalgo', 'vuelta'),
        getParada('hoyaDeLaPlata', 'vuelta'),
        getParada('blasCabrera', 'vuelta'),
        getParada('casablanca', 'vuelta'),
        getParada('elLasso', 'vuelta'),
        getParada('paseoSanJose', 'vuelta'),
        getParada('millerAlto', 'vuelta'),
        getParada('millerBajo', 'vuelta'),
        getParada('sanBernardo', 'vuelta'),
        getParada('mercadoVegueta', 'vuelta')
      ]
    },
    
    // Línea 17: Teatro - Auditorio (por Mesa y López)
    { 
      line: '17', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Auditorio', 
      stopsIda: ['San Telmo', 'Usos Múltiples', 'León y Castillo 50', 'Oficinas Municipales', 'Nicolás Estévanez', 'Mesa y López', 'La Minilla', 'Guanarteme'],
      stopsVuelta: ['Guanarteme Iglesia', 'La Minilla', 'Mesa y López', 'Néstor', 'Oficinas Municipales', 'León y Castillo 35', 'Triana', 'San Telmo'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('usosMultiples', 'ida'),
        getParada('leonCastillo50', 'ida'),
        getParada('oficinasMunicipales', 'ida'),
        getParada('nicolasEstevañez', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('guanarteme', 'ida'),
        getParada('auditorio', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('auditorio', 'vuelta'),
        getParada('guanartemeIglesia', 'vuelta'),
        getParada('laMinilla', 'vuelta'),
        getParada('mesaYLopez', 'vuelta'),
        getParada('nestor', 'vuelta'),
        getParada('oficinasMunicipales', 'vuelta'),
        getParada('leonCastillo35', 'vuelta'),
        getParada('triana', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea 25: Auditorio - Campus Universitario (por Tafira)
    { 
      line: '25', type: 'urban', company: 'municipales', origin: 'Auditorio', destination: 'Campus Universitario', 
      stopsIda: ['Guanarteme', 'La Minilla', 'Pío XII', 'Estadio Insular', 'Tomás Morales 60', 'Lomo Apolinario', 'Cruz de Piedra', 'Tafira Baja', 'Tafira Alta', 'Jardines Jámar'],
      stopsVuelta: ['Jardines Jámar', 'Tafira Alta', 'Tafira Baja', 'Cruz de Piedra', 'Lomo Apolinario', 'Tomás Morales', 'Estadio Insular', 'Pío XII', 'La Minilla', 'Guanarteme Iglesia'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('auditorio', 'ida'),
        getParada('guanarteme', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('pioXII', 'ida'),
        getParada('estadioInsular', 'ida'),
        getParada('tomasMorales60', 'ida'),
        getParada('lomoApolinario', 'ida'),
        getParada('cruzDePiedra', 'ida'),
        getParada('tafiraBaja', 'ida'),
        getParada('tafiraAlta', 'ida'),
        getParada('jardinesJamar', 'ida'),
        getParada('campusULPGC', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('campusULPGC', 'vuelta'),
        getParada('jardinesJamar', 'vuelta'),
        getParada('tafiraAlta', 'vuelta'),
        getParada('tafiraBaja', 'vuelta'),
        getParada('cruzDePiedra', 'vuelta'),
        getParada('lomoApolinario', 'vuelta'),
        getParada('tomasMorales', 'vuelta'),
        getParada('estadioInsular', 'vuelta'),
        getParada('pioXII', 'vuelta'),
        getParada('laMinilla', 'vuelta'),
        getParada('guanartemeIglesia', 'vuelta'),
        getParada('auditorio', 'vuelta')
      ]
    },
    
    // Línea 6: Hoya de La Plata - San Francisco de Paula
    { 
      line: '6', type: 'urban', company: 'municipales', origin: 'Hoya de La Plata', destination: 'San Francisco de Paula', 
      stopsIda: ['Carrefour', 'Salto del Negro', 'Tablero de Gonzalo', 'San Francisco de Paula'],
      stopsVuelta: ['San Francisco de Paula', 'Tablero de Gonzalo', 'Salto del Negro', 'San Cristóbal', 'Carrefour'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('hoyaDeLaPlata', 'ida'),
        getParada('carrefour', 'ida'),
        getParada('saltoDelNegro', 'ida'),
        getParada('tableroGonzalo', 'ida'),
        getParada('sanFranciscoDePaula', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sanFranciscoDePaula', 'vuelta'),
        getParada('tableroGonzalo', 'vuelta'),
        getParada('saltoDelNegro', 'vuelta'),
        getParada('sanCristobal', 'vuelta'),
        getParada('carrefour', 'vuelta'),
        getParada('hoyaDeLaPlata', 'vuelta')
      ]
    },
    
    // Línea 7: Teatro - Campus Universitario (por Lomo Blanco)
    { 
      line: '7', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Campus Universitario', 
      stopsIda: ['San Telmo', 'San Roque', 'Lomo Blanco', 'IES Felo Monzón', 'Ciencias Básicas'],
      stopsVuelta: ['Ingenierías', 'Ciencias Básicas', 'IES Felo Monzón', 'Lomo Blanco', 'San Roque', 'San Telmo'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('sanRoque', 'ida'),
        getParada('lomoBlanco', 'ida'),
        getParada('iesFelo', 'ida'),
        getParada('cienciasBasicas', 'ida'),
        getParada('ingenierias', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('ingenierias', 'vuelta'),
        getParada('cienciasBasicas', 'vuelta'),
        getParada('iesFelo', 'vuelta'),
        getParada('lomoBlanco', 'vuelta'),
        getParada('sanRoque', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea 8: Teatro - Lomo de La Cruz (por Miller Bajo)
    { 
      line: '8', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Lomo de La Cruz', 
      stopsIda: ['San Telmo', 'Plaza del Pino', 'Las Rehoyas', 'Cruz de Piedra', 'Lomo Apolinario', 'Casablanca III'],
      stopsVuelta: ['Lomo de La Cruz', 'Casablanca III', 'Lomo Apolinario', 'Cruz de Piedra', 'Miller Bajo', 'San Telmo'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('plazaDelPino', 'ida'),
        getParada('lasRehoyas', 'ida'),
        getParada('cruzDePiedra', 'ida'),
        getParada('lomoApolinario', 'ida'),
        getParada('casablanca3', 'ida'),
        getParada('lomoDeLaCruz', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lomoDeLaCruz', 'vuelta'),
        getParada('casablanca3', 'vuelta'),
        getParada('lomoApolinario', 'vuelta'),
        getParada('cruzDePiedra', 'vuelta'),
        getParada('millerBajo', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea 9: Hoya de La Plata - Hospital Dr. Negrín
    { 
      line: '9', type: 'urban', company: 'municipales', origin: 'Hoya de La Plata', destination: 'Hospital Dr. Negrín', 
      stopsIda: ['Carrefour', 'Teatro', 'San Telmo', 'Las Rehoyas', 'Don Pedro Infinito', 'Escaleritas', 'Cementerio del Puerto'],
      stopsVuelta: ['Hospital Dr. Negrín', 'Cementerio del Puerto', 'Escaleritas', 'Don Pedro Infinito', 'Miller Bajo', 'Teatro', 'Carrefour'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('hoyaDeLaPlata', 'ida'),
        getParada('carrefour', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('lasRehoyas', 'ida'),
        getParada('donPedroInfinito', 'ida'),
        getParada('escaleritas', 'ida'),
        getParada('cementerioDelPuerto', 'ida'),
        getParada('hospitalNegrin', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('hospitalNegrin', 'vuelta'),
        getParada('cementerioDelPuerto', 'vuelta'),
        getParada('escaleritas', 'vuelta'),
        getParada('donPedroInfinito', 'vuelta'),
        getParada('millerBajo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('carrefour', 'vuelta'),
        getParada('hoyaDeLaPlata', 'vuelta')
      ]
    },
    
    // Línea 10: Teatro - Hospital Dr. Negrín (Exprés)
    { 
      line: '10', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Hospital Dr. Negrín', 
      stopsIda: ['San Telmo', 'Usos Múltiples', 'Juan XXIII', 'Cementerio del Puerto'],
      stopsVuelta: ['Hospital Dr. Negrín', 'Cementerio del Puerto', 'Parque Doramas', 'Juan XXIII', 'San Telmo'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('usosMultiples', 'ida'),
        getParada('juanXXIII', 'ida'),
        getParada('cementerioDelPuerto', 'ida'),
        getParada('hospitalNegrin', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('hospitalNegrin', 'vuelta'),
        getParada('cementerioDelPuerto', 'vuelta'),
        getParada('parqueDoramas', 'vuelta'),
        getParada('juanXXIII', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea 18: Puerto - Zona Portuaria
    { 
      line: '18', type: 'urban', company: 'municipales', origin: 'Puerto', destination: 'Zona Portuaria', 
      stopsIda: ['Autoridad Portuaria', 'Los Cambulloneros', 'Muelle León y Castillo', 'Petrolíferas', 'Astican', 'Nelson Mandela'],
      stopsVuelta: ['Nelson Mandela', 'Petrolíferas', 'Astican', 'Muelle León y Castillo', 'Autoridad Portuaria'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('puerto', 'ida'),
        getParada('autoridadPortuaria', 'ida'),
        getParada('losCambulloneros', 'ida'),
        getParada('muelleLeonCastillo', 'ida'),
        getParada('petroliferas', 'ida'),
        getParada('astican', 'ida'),
        getParada('nelsonMandela', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('nelsonMandela', 'vuelta'),
        getParada('petroliferas', 'vuelta'),
        getParada('astican', 'vuelta'),
        getParada('muelleLeonCastillo', 'vuelta'),
        getParada('autoridadPortuaria', 'vuelta'),
        getParada('puerto', 'vuelta')
      ]
    },
    
    // Línea 19: Santa Catalina - El Sebadal
    { 
      line: '19', type: 'urban', company: 'municipales', origin: 'Santa Catalina', destination: 'El Sebadal', 
      stopsIda: ['Woermann', 'Castillo de La Luz', 'Puerto', 'Catamarca', 'Arequipa', 'Guaguas Municipales'],
      stopsVuelta: ['El Sebadal', 'Arequipa', 'Guaguas Municipales', 'Catamarca', 'Puerto', 'Mercado del Puerto'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('woermann', 'ida'),
        getParada('castilloDeLaLuz', 'ida'),
        getParada('puerto', 'ida'),
        getParada('catamarca', 'ida'),
        getParada('arequipa', 'ida'),
        getParada('elSebadal', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('elSebadal', 'vuelta'),
        getParada('arequipa', 'vuelta'),
        getParada('catamarca', 'vuelta'),
        getParada('puerto', 'vuelta'),
        getParada('mercadoDelPuerto', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 20: Santa Catalina - La Isleta
    { 
      line: '20', type: 'urban', company: 'municipales', origin: 'Santa Catalina', destination: 'La Isleta', 
      stopsIda: ['Woermann', 'Juan Rejón', 'Luján Pérez', 'Tinguaro', 'La Naval'],
      stopsVuelta: ['Puerto', 'Bandama', 'Las Coloradas', 'Nueva Isleta', 'Faro', 'Mercado del Puerto'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('woermann', 'ida'),
        getParada('juanRejon', 'ida'),
        getParada('lujanPerez', 'ida'),
        getParada('tinguaro', 'ida'),
        getParada('laNaval', 'ida'),
        getParada('puerto', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('puerto', 'vuelta'),
        getParada('bandama', 'vuelta'),
        getParada('lasColoradas', 'vuelta'),
        getParada('nuevaIsleta', 'vuelta'),
        getParada('faro', 'vuelta'),
        getParada('mercadoDelPuerto', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 21: Puerto - Escaleritas - La Feria - Hospital Dr. Negrín - Santa Catalina (Circular)
    { 
      line: '21', type: 'urban', company: 'municipales', origin: 'Puerto', destination: 'Santa Catalina', 
      stopsIda: ['Castillo de La Luz', 'Santa Catalina', 'El Corte Inglés', 'Estadio Insular', 'Escaleritas', 'CC La Ballena', 'La Feria', 'Hospital Dr. Negrín', 'La Minilla', 'Mesa y López', 'Base Naval'],
      stopsVuelta: [],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('puerto', 'ida'),
        getParada('castilloDeLaLuz', 'ida'),
        getParada('santaCatalina', 'ida'),
        getParada('elCorteIngles', 'ida'),
        getParada('estadioInsular', 'ida'),
        getParada('escaleritas', 'ida'),
        getParada('ccLaBallena', 'ida'),
        getParada('laFeria', 'ida'),
        getParada('hospitalNegrin', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('baseNaval', 'ida'),
        getParada('intercambiadorSantaCatalina', 'ida')
      ],
      routeCoordsVuelta: []
    },
    
    // Línea 22: Santa Catalina - La Paterna
    { 
      line: '22', type: 'urban', company: 'municipales', origin: 'Santa Catalina', destination: 'La Paterna', 
      stopsIda: ['El Corte Inglés', 'Plaza de España', 'La Minilla', 'Guanarteme', 'Lomo Apolinario'],
      stopsVuelta: ['La Paterna', 'Lomo Apolinario', 'La Minilla', 'Plaza de España', 'El Corte Inglés'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('elCorteIngles', 'ida'),
        getParada('plazaDeEspana', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('guanarteme', 'ida'),
        getParada('lomoApolinario', 'ida'),
        getParada('laPaterna', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('laPaterna', 'vuelta'),
        getParada('lomoApolinario', 'vuelta'),
        getParada('laMinilla', 'vuelta'),
        getParada('plazaDeEspana', 'vuelta'),
        getParada('elCorteIngles', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 24: Santa Catalina - Hospital Dr. Negrín - La Feria - Escaleritas - Puerto (Circular complementaria a 21)
    { 
      line: '24', type: 'urban', company: 'municipales', origin: 'Santa Catalina', destination: 'Puerto', 
      stopsIda: ['Intercambiador Santa Catalina', 'Base Naval', 'Mesa y López', 'La Minilla', 'Hospital Dr. Negrín', 'La Feria', 'CC La Ballena', 'Escaleritas', 'Santa Catalina', 'Puerto'],
      stopsVuelta: [],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('intercambiadorSantaCatalina', 'ida'),
        getParada('baseNaval', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('hospitalNegrin', 'ida'),
        getParada('laFeria', 'ida'),
        getParada('ccLaBallena', 'ida'),
        getParada('escaleritas', 'ida'),
        getParada('santaCatalina', 'ida'),
        getParada('puerto', 'ida')
      ],
      routeCoordsVuelta: []
    },
    
    // Línea 26: Santa Catalina - Campus Universitario (por Siete Palmas)
    { 
      line: '26', type: 'urban', company: 'municipales', origin: 'Santa Catalina', destination: 'Campus Universitario', 
      stopsIda: ['Mesa y López', 'La Minilla', 'Siete Palmas', 'Hospital Dr. Negrín', 'La Ballena', 'Tafira'],
      stopsVuelta: ['Campus Universitario', 'Tafira', 'La Ballena', 'Siete Palmas', 'La Minilla', 'Mesa y López'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('sietePalmas', 'ida'),
        getParada('hospitalNegrin', 'ida'),
        getParada('ccLaBallena', 'ida'),
        getParada('tafiraBaja', 'ida'),
        getParada('campusULPGC', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('campusULPGC', 'vuelta'),
        getParada('tafiraBaja', 'vuelta'),
        getParada('ccLaBallena', 'vuelta'),
        getParada('sietePalmas', 'vuelta'),
        getParada('laMinilla', 'vuelta'),
        getParada('mesaYLopez', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 33: Guiniguada - Puerto (por Ciudad Alta)
    { 
      line: '33', type: 'urban', company: 'municipales', origin: 'Guiniguada', destination: 'Puerto', 
      stopsIda: ['San Bernardo', 'Peregrina', 'Ciudad Alta', 'Altavista', 'Las Chumberas', 'La Minilla', 'Mesa y López', 'Santa Catalina'],
      stopsVuelta: ['Puerto', 'Santa Catalina', 'Mesa y López', 'La Minilla', 'Las Chumberas', 'Altavista', 'Ciudad Alta', 'San Bernardo'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('guiniguada', 'ida'),
        getParada('sanBernardo', 'ida'),
        getParada('peregrina', 'ida'),
        getParada('ciudadAlta', 'ida'),
        getParada('altavista', 'ida'),
        getParada('lasChumberas', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('santaCatalina', 'ida'),
        getParada('puerto', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('puerto', 'vuelta'),
        getParada('santaCatalina', 'vuelta'),
        getParada('mesaYLopez', 'vuelta'),
        getParada('laMinilla', 'vuelta'),
        getParada('lasChumberas', 'vuelta'),
        getParada('altavista', 'vuelta'),
        getParada('ciudadAlta', 'vuelta'),
        getParada('sanBernardo', 'vuelta'),
        getParada('guiniguada', 'vuelta')
      ]
    },
    
    // Línea 35: Auditorio - La Ballena (por Las Torres)
    { 
      line: '35', type: 'urban', company: 'municipales', origin: 'Auditorio', destination: 'La Ballena', 
      stopsIda: ['Guanarteme', 'La Minilla', 'Siete Palmas', 'Las Torres', 'Hospital Dr. Negrín'],
      stopsVuelta: ['CC La Ballena', 'Las Torres', 'Siete Palmas', 'La Minilla', 'Guanarteme'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('auditorio', 'ida'),
        getParada('guanarteme', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('sietePalmas', 'ida'),
        getParada('lasTorres', 'ida'),
        getParada('hospitalNegrin', 'ida'),
        getParada('ccLaBallena', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('ccLaBallena', 'vuelta'),
        getParada('lasTorres', 'vuelta'),
        getParada('sietePalmas', 'vuelta'),
        getParada('laMinilla', 'vuelta'),
        getParada('guanarteme', 'vuelta'),
        getParada('auditorio', 'vuelta')
      ]
    },

    // Línea 41: Santa Catalina - Las Coloradas
    { 
      line: '41', type: 'urban', company: 'municipales', origin: 'Santa Catalina', destination: 'Las Coloradas', 
      stopsIda: ['Puerto', 'Juan Rejón', 'La Isleta', 'Nueva Isleta', 'Las Coloradas'],
      stopsVuelta: ['Las Coloradas', 'Nueva Isleta', 'La Isleta', 'Juan Rejón', 'Puerto'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('puerto', 'ida'),
        getParada('juanRejon', 'ida'),
        getParada('laIsleta', 'ida'),
        getParada('nuevaIsleta', 'ida'),
        getParada('lasColoradas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lasColoradas', 'vuelta'),
        getParada('nuevaIsleta', 'vuelta'),
        getParada('laIsleta', 'vuelta'),
        getParada('juanRejon', 'vuelta'),
        getParada('puerto', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 44: Santa Catalina - Isla Perdida
    { 
      line: '44', type: 'urban', company: 'municipales', origin: 'Santa Catalina', destination: 'Isla Perdida', 
      stopsIda: ['Puerto', 'Juan Rejón', 'La Isleta', 'Nueva Isleta', 'Isla Perdida'],
      stopsVuelta: ['Isla Perdida', 'Nueva Isleta', 'La Isleta', 'Juan Rejón', 'Puerto'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('puerto', 'ida'),
        getParada('juanRejon', 'ida'),
        getParada('laIsleta', 'ida'),
        getParada('nuevaIsleta', 'ida'),
        getParada('islaPerdida', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('islaPerdida', 'vuelta'),
        getParada('nuevaIsleta', 'vuelta'),
        getParada('laIsleta', 'vuelta'),
        getParada('juanRejon', 'vuelta'),
        getParada('puerto', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 45: Auditorio - Hoya Andrea
    { 
      line: '45', type: 'urban', company: 'municipales', origin: 'Auditorio', destination: 'Hoya Andrea', 
      stopsIda: ['Guanarteme', 'La Minilla', 'Siete Palmas', 'La Feria', 'Las Torres', 'Hoya Andrea'],
      stopsVuelta: ['Hoya Andrea', 'Las Torres', 'La Feria', 'Siete Palmas', 'La Minilla', 'Guanarteme'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('auditorio', 'ida'),
        getParada('guanarteme', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('sietePalmas', 'ida'),
        getParada('laFeria', 'ida'),
        getParada('lasTorres', 'ida'),
        getParada('hoyaAndrea', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('hoyaAndrea', 'vuelta'),
        getParada('lasTorres', 'vuelta'),
        getParada('laFeria', 'vuelta'),
        getParada('sietePalmas', 'vuelta'),
        getParada('laMinilla', 'vuelta'),
        getParada('guanarteme', 'vuelta'),
        getParada('auditorio', 'vuelta')
      ]
    },
    
    // Línea 47: Auditorio - Tamaraceite (por Lomo Los Frailes)
    { 
      line: '47', type: 'urban', company: 'municipales', origin: 'Auditorio', destination: 'Tamaraceite', 
      stopsIda: ['Guanarteme', 'La Minilla', 'Siete Palmas', 'Las Torres', 'Lomo Los Frailes'],
      stopsVuelta: ['Tamaraceite', 'Lomo Los Frailes', 'Las Torres', 'Siete Palmas', 'La Minilla', 'Guanarteme'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('auditorio', 'ida'),
        getParada('guanarteme', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('sietePalmas', 'ida'),
        getParada('lasTorres', 'ida'),
        getParada('lomoLosFrailes', 'ida'),
        getParada('tamaraceite', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tamaraceite', 'vuelta'),
        getParada('lomoLosFrailes', 'vuelta'),
        getParada('lasTorres', 'vuelta'),
        getParada('sietePalmas', 'vuelta'),
        getParada('laMinilla', 'vuelta'),
        getParada('guanarteme', 'vuelta'),
        getParada('auditorio', 'vuelta')
      ]
    },
    
    // Línea 48: Escaleritas - Campus Universitario
    { 
      line: '48', type: 'urban', company: 'municipales', origin: 'Escaleritas', destination: 'Campus Universitario', 
      stopsIda: ['Av. Escaleritas', 'Cruz de Piedra', 'Tafira Baja', 'Tafira Alta'],
      stopsVuelta: ['Campus Universitario', 'Tafira Alta', 'Tafira Baja', 'Cruz de Piedra', 'Av. Escaleritas'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('escaleritas', 'ida'),
        getParada('cruzDePiedra', 'ida'),
        getParada('tafiraBaja', 'ida'),
        getParada('tafiraAlta', 'ida'),
        getParada('campusULPGC', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('campusULPGC', 'vuelta'),
        getParada('tafiraAlta', 'vuelta'),
        getParada('tafiraBaja', 'vuelta'),
        getParada('cruzDePiedra', 'vuelta'),
        getParada('escaleritas', 'vuelta')
      ]
    },
    
    // Línea 49: Puerto - Auditorio
    { 
      line: '49', type: 'urban', company: 'municipales', origin: 'Puerto', destination: 'Auditorio', 
      stopsIda: ['Castillo de La Luz', 'Mercado del Puerto', 'Woermann', 'Parque Santa Catalina', 'Tomás Miller', 'Fernando Guanarteme', 'Simancas', 'Numancia', 'Auditorio'],
      stopsVuelta: ['Auditorio', 'CC Las Arenas', 'Castillejos', 'Mesa y López', 'Plaza de España', 'Santa Catalina', 'Woermann', 'Castillo de La Luz', 'Puerto'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('puerto', 'ida'),
        getParada('castilloLuz', 'ida'),
        getParada('mercadoPuerto', 'ida'),
        getParada('santaCatalina', 'ida'),
        getParada('tomasMiller', 'ida'),
        getParada('fernandoGuanarteme', 'ida'),
        getParada('auditorio', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('auditorio', 'vuelta'),
        getParada('lasArenas', 'vuelta'),
        getParada('mesaYLopez', 'vuelta'),
        getParada('santaCatalina', 'vuelta'),
        getParada('puerto', 'vuelta')
      ]
    },
    
    // Línea 50: Ciudad Deportiva Gran Canaria - Zárate
    { 
      line: '50', type: 'urban', company: 'municipales', origin: 'Ciudad Deportiva Gran Canaria', destination: 'Zárate', 
      stopsIda: ['Dr. Sventenius', 'Francisco Inglott Artiles', 'Juan Sánchez de la Coba', 'Sabino Berthelot', 'Zárate'],
      stopsVuelta: ['Zárate', 'Sabino Berthelot', 'Juan Sánchez de la Coba', 'Francisco Inglott Artiles', 'Ciudad Deportiva Gran Canaria'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('ciudadDeportivaGC', 'ida'),
        getParada('zarate', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('zarate', 'vuelta'),
        getParada('ciudadDeportivaGC', 'vuelta')
      ]
    },
    
    // Línea 51: Hoya de La Plata - Casablanca
    { 
      line: '51', type: 'urban', company: 'municipales', origin: 'Hoya de La Plata', destination: 'Casablanca', 
      stopsIda: ['Blas Cabrera Felipe (Carrefour)', 'Ciudad Deportiva Gran Canaria', 'Amurga (C.S. Cono Sur)', 'Amurga (Casablanca)', 'Gavilán (Los Granjeros)'],
      stopsVuelta: ['Gavilán (Los Granjeros)', 'Schubert', 'Pedro Hidalgo', 'Sargento Salom', 'Blas Cabrera Felipe (Carrefour)', 'Hoya de La Plata'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('hoyaDeLaPlata', 'ida'),
        getParada('ciudadDeportivaGC', 'ida'),
        getParada('casablanca', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('casablanca', 'vuelta'),
        getParada('ciudadDeportivaGC', 'vuelta'),
        getParada('hoyaDeLaPlata', 'vuelta')
      ]
    },
    
    // Línea 52: Hoya de La Plata - Pedro Hidalgo
    { 
      line: '52', type: 'urban', company: 'municipales', origin: 'Hoya de La Plata', destination: 'Pedro Hidalgo', 
      stopsIda: ['Blas Cabrera Felipe (Carrefour)', 'Sargento Salom', 'Pedro Hidalgo', 'San Cristóbal de La Laguna', 'Salamanca'],
      stopsVuelta: ['Salamanca', 'Pedro Hidalgo', 'Sargento Salom', 'Blas Cabrera Felipe (Carrefour)', 'Hoya de La Plata'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('hoyaDeLaPlata', 'ida'),
        getParada('pedroHidalgo', 'ida'),
        getParada('salamanca', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('salamanca', 'vuelta'),
        getParada('pedroHidalgo', 'vuelta'),
        getParada('hoyaDeLaPlata', 'vuelta')
      ]
    },
    
    // Línea 54: Teatro - San Juan
    { 
      line: '54', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'San Juan', 
      stopsIda: ['San Telmo', 'Vegueta', 'San Juan'],
      stopsVuelta: ['San Juan', 'Vegueta', 'Triana'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('vegueta', 'ida'),
        getParada('sanJuan', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sanJuan', 'vuelta'),
        getParada('vegueta', 'vuelta'),
        getParada('triana', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea 55: Ciudad Deportiva Gran Canaria - El Lasso
    { 
      line: '55', type: 'urban', company: 'municipales', origin: 'Ciudad Deportiva Gran Canaria', destination: 'El Lasso', 
      stopsIda: ['Amurga (C.S. Cono Sur)', 'Campo de fútbol El Lasso', 'Amurga (El Lasso)', 'Amurga (iglesia)', 'Ciudad de San Juan de Dios (El Lasso)'],
      stopsVuelta: ['Ciudad de San Juan de Dios (El Lasso)', 'Amurga (iglesia)', 'Amurga (El Lasso)', 'Campo de fútbol El Lasso', 'Amurga (C.S. Cono Sur)', 'Ciudad Deportiva Gran Canaria'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('ciudadDeportivaGC', 'ida'),
        getParada('elLasso', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('elLasso', 'vuelta'),
        getParada('ciudadDeportivaGC', 'vuelta')
      ]
    },
    
    // Línea 70: Teatro - El Secadero
    { 
      line: '70', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'El Secadero', 
      stopsIda: ['San Telmo', 'Guiniguada', 'Miller Bajo', 'El Batán', 'El Secadero'],
      stopsVuelta: ['El Secadero', 'El Batán', 'Miller Alto', 'Guiniguada', 'Triana'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('guiniguada', 'ida'),
        getParada('millerBajo', 'ida'),
        getParada('elBatan', 'ida'),
        getParada('elSecadero', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('elSecadero', 'vuelta'),
        getParada('elBatan', 'vuelta'),
        getParada('millerAlto', 'vuelta'),
        getParada('guiniguada', 'vuelta'),
        getParada('triana', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea 80: Teatro - San Francisco
    { 
      line: '80', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'San Francisco', 
      stopsIda: ['San Telmo', 'Guiniguada', 'Miller Bajo', 'Las Rehoyas', 'Hospital Juan Carlos I', 'San Francisco'],
      stopsVuelta: ['San Francisco', 'Hospital Juan Carlos I', 'Las Rehoyas', 'Miller Alto', 'Guiniguada', 'Triana'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('guiniguada', 'ida'),
        getParada('millerBajo', 'ida'),
        getParada('lasRehoyas', 'ida'),
        getParada('hospitalJuanCarlosI', 'ida'),
        getParada('sanFrancisco', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sanFrancisco', 'vuelta'),
        getParada('hospitalJuanCarlosI', 'vuelta'),
        getParada('lasRehoyas', 'vuelta'),
        getParada('millerAlto', 'vuelta'),
        getParada('guiniguada', 'vuelta'),
        getParada('triana', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea 81: Santa Catalina - Lomo de La Cruz
    { 
      line: '81', type: 'urban', company: 'municipales', origin: 'Santa Catalina', destination: 'Lomo de La Cruz', 
      stopsIda: ['Mesa y López', 'Pío XII', 'Tomás Morales', 'Carretera Mata', 'Lomo de La Cruz'],
      stopsVuelta: ['Lomo de La Cruz', 'Carretera Mata', 'Tomás Morales', 'Pío XII', 'Mesa y López'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('pioXII', 'ida'),
        getParada('tomasMorales', 'ida'),
        getParada('carreteraMata', 'ida'),
        getParada('lomoDeLaCruz', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lomoDeLaCruz', 'vuelta'),
        getParada('carreteraMata', 'vuelta'),
        getParada('tomasMorales', 'vuelta'),
        getParada('pioXII', 'vuelta'),
        getParada('mesaYLopez', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 82: Teatro - La Paterna
    { 
      line: '82', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'La Paterna', 
      stopsIda: ['San Telmo', 'Guiniguada', 'Las Rehoyas', 'La Ballena', 'La Paterna'],
      stopsVuelta: ['La Paterna', 'La Ballena', 'Las Rehoyas', 'Guiniguada', 'Triana'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('guiniguada', 'ida'),
        getParada('lasRehoyas', 'ida'),
        getParada('laBallena', 'ida'),
        getParada('laPaterna', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('laPaterna', 'vuelta'),
        getParada('laBallena', 'vuelta'),
        getParada('lasRehoyas', 'vuelta'),
        getParada('guiniguada', 'vuelta'),
        getParada('triana', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea 84: Teatro - Lomo de La Cruz (por San Francisco - Fines de semana)
    { 
      line: '84', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Lomo de La Cruz', 
      stopsIda: ['San Telmo', 'Guiniguada', 'Miller Bajo', 'San Francisco', 'Casablanca', 'Lomo de La Cruz'],
      stopsVuelta: ['Lomo de La Cruz', 'Casablanca', 'San Francisco', 'Miller Alto', 'Guiniguada', 'Triana'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('guiniguada', 'ida'),
        getParada('millerBajo', 'ida'),
        getParada('sanFrancisco', 'ida'),
        getParada('casablanca', 'ida'),
        getParada('lomoDeLaCruz', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lomoDeLaCruz', 'vuelta'),
        getParada('casablanca', 'vuelta'),
        getParada('sanFrancisco', 'vuelta'),
        getParada('millerAlto', 'vuelta'),
        getParada('guiniguada', 'vuelta'),
        getParada('triana', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea 91: Teatro - Tamaraceite
    { 
      line: '91', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Tamaraceite', 
      stopsIda: ['San Telmo', 'Tomás Morales', 'Las Rehoyas', 'Siete Palmas', 'Tamaraceite'],
      stopsVuelta: ['Tamaraceite', 'Siete Palmas', 'Las Rehoyas', 'Tomás Morales', 'Triana'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('tomasMorales', 'ida'),
        getParada('lasRehoyas', 'ida'),
        getParada('sietePalmas', 'ida'),
        getParada('tamaraceite', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tamaraceite', 'vuelta'),
        getParada('sietePalmas', 'vuelta'),
        getParada('lasRehoyas', 'vuelta'),
        getParada('tomasMorales', 'vuelta'),
        getParada('triana', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea X11: Teatro - Las Torres (Exprés)
    { 
      line: 'X11', type: 'urban', company: 'municipales', origin: 'Teatro', destination: 'Las Torres', 
      stopsIda: ['San Telmo', 'Tomás Morales', 'Las Chumberas', 'La Feria', 'La Ballena', 'Las Torres'],
      stopsVuelta: ['Las Torres', 'La Ballena', 'La Feria', 'Las Chumberas', 'Tomás Morales', 'San Telmo'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('tomasMorales', 'ida'),
        getParada('lasChumberas', 'ida'),
        getParada('laFeria', 'ida'),
        getParada('laBallena', 'ida'),
        getParada('lasTorres', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lasTorres', 'vuelta'),
        getParada('laBallena', 'vuelta'),
        getParada('laFeria', 'vuelta'),
        getParada('lasChumberas', 'vuelta'),
        getParada('tomasMorales', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // Línea X47: Puerto - Tamaraceite (Exprés)
    { 
      line: 'X47', type: 'urban', company: 'municipales', origin: 'Puerto', destination: 'Tamaraceite', 
      stopsIda: ['Santa Catalina', 'Mesa y López', 'Hospital Negrín', 'Siete Palmas', 'Tamaraceite'],
      stopsVuelta: ['Tamaraceite', 'Siete Palmas', 'Hospital Negrín', 'Mesa y López', 'Santa Catalina'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('puerto', 'ida'),
        getParada('santaCatalina', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('hospitalNegrin', 'ida'),
        getParada('sietePalmas', 'ida'),
        getParada('tamaraceite', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tamaraceite', 'vuelta'),
        getParada('sietePalmas', 'vuelta'),
        getParada('hospitalNegrin', 'vuelta'),
        getParada('mesaYLopez', 'vuelta'),
        getParada('santaCatalina', 'vuelta'),
        getParada('puerto', 'vuelta')
      ]
    },
    
    // Línea 32: Guiniguada - Auditorio (por San Antonio)
    { 
      line: '32', type: 'urban', company: 'municipales', origin: 'Guiniguada', destination: 'Auditorio', 
      stopsIda: ['San Telmo', 'Tomás Morales', 'Paseo San Antonio', 'Schamann', 'Escaleritas', 'Guanarteme', 'Auditorio'],
      stopsVuelta: ['Auditorio', 'Guanarteme', 'Escaleritas', 'Schamann', 'Paseo San Antonio', 'Tomás Morales', 'San Telmo'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('guiniguada', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('tomasMorales', 'ida'),
        getParada('paseoSanAntonio', 'ida'),
        getParada('schamann', 'ida'),
        getParada('escaleritas', 'ida'),
        getParada('guanarteme', 'ida'),
        getParada('auditorio', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('auditorio', 'vuelta'),
        getParada('guanarteme', 'vuelta'),
        getParada('escaleritas', 'vuelta'),
        getParada('schamann', 'vuelta'),
        getParada('paseoSanAntonio', 'vuelta'),
        getParada('tomasMorales', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('guiniguada', 'vuelta')
      ]
    },
    
    // Línea 46: Tamaraceite - Siete Palmas (por San Lorenzo)
    { 
      line: '46', type: 'urban', company: 'municipales', origin: 'Tamaraceite', destination: 'Siete Palmas', 
      stopsIda: ['San Lorenzo', 'Almatriche', 'Hoya Andrea', 'Siete Palmas'],
      stopsVuelta: ['Siete Palmas', 'Hoya Andrea', 'Almatriche', 'San Lorenzo'],
      color: '#FDB913',
      routeCoordsIda: [
        getParada('tamaraceite', 'ida'),
        getParada('sanLorenzo', 'ida'),
        getParada('almatriche', 'ida'),
        getParada('hoyaAndrea', 'ida'),
        getParada('sietePalmas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sietePalmas', 'vuelta'),
        getParada('hoyaAndrea', 'vuelta'),
        getParada('almatriche', 'vuelta'),
        getParada('sanLorenzo', 'vuelta'),
        getParada('tamaraceite', 'vuelta')
      ]
    },
    
    // ========== GLOBAL (Interurbanas - Azules) ==========
    
    // Línea 1 Global: Las Palmas - Puerto de Mogán
    { 
      line: '1', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Puerto de Mogán', 
      stopsIda: ['Teatro', 'San Telmo', 'Jinámar', 'Telde Intercambiador', 'Ingenio', 'Carrizal', 'Vecindario', 'San Agustín', 'Playa del Inglés'],
      stopsVuelta: ['Playa del Inglés', 'San Agustín', 'Vecindario', 'Carrizal', 'Ingenio', 'Telde', 'Jinámar', 'San Telmo', 'Teatro', 'Parque Santa Catalina'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('ingenio', 'ida'),
        getParada('carrizal', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('sanAgustin', 'ida'),
        getParada('playaIngles', 'ida'),
        getParada('maspalomas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('maspalomas', 'vuelta'),
        getParada('playaIngles', 'vuelta'),
        getParada('sanAgustin', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('carrizal', 'vuelta'),
        getParada('ingenio', 'vuelta'),
        getParada('telde', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('parqueSantaCatalina', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 30 Global: Las Palmas - Maspalomas (por Aeropuerto)
    { 
      line: '30', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Faro de Maspalomas', 
      stopsIda: ['Teatro', 'Telde', 'Aeropuerto', 'Cruce de Arinaga', 'Vecindario', 'San Agustín', 'Playa del Inglés'],
      stopsVuelta: ['Playa del Inglés', 'San Agustín', 'Vecindario', 'Cruce de Arinaga', 'Aeropuerto', 'Telde Intercambiador', 'Teatro', 'Parque Santa Catalina'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('telde', 'ida'),
        getParada('aeropuerto', 'ida'),
        getParada('cruce_de_arinaga', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('sanAgustin', 'ida'),
        getParada('playaIngles', 'ida'),
        getParada('maspalomas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('maspalomas', 'vuelta'),
        getParada('playaIngles', 'vuelta'),
        getParada('sanAgustin', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('cruce_de_arinaga', 'vuelta'),
        getParada('aeropuerto', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('parqueSantaCatalina', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 60 Global: Las Palmas - Aeropuerto (Directo)
    { 
      line: '60', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Aeropuerto', 
      stopsIda: ['Parque Santa Catalina', 'Julio Verne', 'Las Arenas', 'Teatro', 'Jinámar', 'Telde'],
      stopsVuelta: ['Telde Intercambiador', 'Jinámar', 'San Telmo', 'Teatro', 'Canteras', 'Ferreras', 'Parque Santa Catalina'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('parqueSantaCatalina', 'ida'),
        getParada('julioVerne', 'ida'),
        getParada('lasArenas', 'ida'),
        getParada('teatro', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('telde', 'ida'),
        getParada('aeropuerto', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('aeropuerto', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('canteras', 'vuelta'),
        getParada('ferreras', 'vuelta'),
        getParada('parqueSantaCatalina', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 80 Global: Las Palmas - Telde (por costa)
    { 
      line: '80', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Telde', 
      stopsIda: ['Teatro', 'San Telmo', 'Hoya de La Plata', 'Jinámar', 'Melenara', 'Salinetas'],
      stopsVuelta: ['Salinetas', 'Melenara', 'Jinámar', 'Hoya de La Plata', 'Miller Bajo', 'San Telmo', 'Teatro', 'Parque Santa Catalina'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('hoyaDeLaPlata', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('melenara', 'ida'),
        getParada('salinetas', 'ida'),
        getParada('telde', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('telde', 'vuelta'),
        getParada('salinetas', 'vuelta'),
        getParada('melenara', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('hoyaDeLaPlata', 'vuelta'),
        getParada('millerBajo', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('parqueSantaCatalina', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 91 Global: Santa Catalina - Puerto de Mogán (directo)
    { 
      line: '91', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Puerto de Mogán', 
      stopsIda: ['Teatro', 'Telde Intercambiador', 'Agüimes', 'Cruce de Arinaga', 'Vecindario', 'San Agustín'],
      stopsVuelta: ['San Agustín', 'Vecindario', 'Cruce de Arinaga', 'Agüimes', 'Telde', 'Teatro', 'Parque Santa Catalina'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('aguimes', 'ida'),
        getParada('cruce_de_arinaga', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('sanAgustin', 'ida'),
        getParada('maspalomas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('maspalomas', 'vuelta'),
        getParada('sanAgustin', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('cruce_de_arinaga', 'vuelta'),
        getParada('aguimes', 'vuelta'),
        getParada('telde', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('parqueSantaCatalina', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 5 Global: Las Palmas - Faro de Maspalomas
    { 
      line: '5', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Faro de Maspalomas', 
      stopsIda: ['Teatro', 'San Telmo', 'Guiniguada', 'Jinámar', 'Telde Intercambiador'],
      stopsVuelta: ['Telde Intercambiador', 'Jinámar', 'Guiniguada', 'San Telmo', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('guiniguada', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('guiniguada', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 10 Global: Las Palmas - Sardina del Sur
    { 
      line: '10', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Sardina del Sur', 
      stopsIda: ['Teatro', 'San Telmo', 'Jinámar', 'Telde Intercambiador', 'Aeropuerto'],
      stopsVuelta: ['Aeropuerto', 'Telde Intercambiador', 'Jinámar', 'San Telmo', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('aeropuerto', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('aeropuerto', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 11 Global: Las Palmas - Agüimes
    { 
      line: '11', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Agüimes', 
      stopsIda: ['Teatro', 'San Telmo', 'Jinámar', 'Telde Intercambiador', 'Ingenio', 'Agüimes'],
      stopsVuelta: ['Agüimes', 'Ingenio', 'Telde Intercambiador', 'Jinámar', 'San Telmo', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('ingenio', 'ida'),
        getParada('aguimes', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('aguimes', 'vuelta'),
        getParada('ingenio', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 12 Global: Las Palmas - Telde (circular)
    { 
      line: '12', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Telde', 
      stopsIda: ['Teatro', 'San Telmo', 'Jinámar', 'Telde Intercambiador', 'Agüimes', 'Cruce de Arinaga', 'Arinaga'],
      stopsVuelta: ['Arinaga', 'Cruce de Arinaga', 'Agüimes', 'Telde Intercambiador', 'Jinámar', 'San Telmo', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('aguimes', 'ida'),
        getParada('cruce_de_arinaga', 'ida'),
        getParada('arinaga', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('arinaga', 'vuelta'),
        getParada('cruce_de_arinaga', 'vuelta'),
        getParada('aguimes', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 15 Global: Las Palmas - Las Remudas
    { 
      line: '15', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Las Remudas', 
      stopsIda: ['Teatro', 'San Telmo', 'Jinámar', 'Telde Intercambiador', 'Salinetas', 'Melenara'],
      stopsVuelta: ['Melenara', 'Salinetas', 'Telde Intercambiador', 'Jinámar', 'San Telmo', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('salinetas', 'ida'),
        getParada('melenara', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('melenara', 'vuelta'),
        getParada('salinetas', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 18 Global: Faro de Maspalomas - Tejeda (por San Bartolomé y Roque Nublo)
    { 
      line: '18', type: 'interurban', company: 'global', origin: 'Faro de Maspalomas', destination: 'Tejeda', 
      stopsIda: ['Teatro', 'San Telmo', 'Jinámar', 'Telde Intercambiador', 'Carrizal', 'Vecindario'],
      stopsVuelta: ['Vecindario', 'Carrizal', 'Telde Intercambiador', 'Jinámar', 'San Telmo', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('carrizal', 'ida'),
        getParada('vecindario', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('vecindario', 'vuelta'),
        getParada('carrizal', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 21 Global: Las Palmas - Agüimes (semidirecto)
    { 
      line: '21', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Agüimes', 
      stopsIda: ['Teatro', 'San Telmo', 'Jinámar', 'Telde Intercambiador', 'Ingenio', 'Carrizal', 'Vecindario', 'San Agustín'],
      stopsVuelta: ['San Agustín', 'Vecindario', 'Carrizal', 'Ingenio', 'Telde Intercambiador', 'Jinámar', 'San Telmo', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('ingenio', 'ida'),
        getParada('carrizal', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('sanAgustin', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sanAgustin', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('carrizal', 'vuelta'),
        getParada('ingenio', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 25 Global: Playa de Arinaga - Faro de Maspalomas
    { 
      line: '25', type: 'interurban', company: 'global', origin: 'Playa de Arinaga', destination: 'Faro de Maspalomas', 
      stopsIda: ['Teatro', 'Jinámar', 'Telde Intercambiador', 'Vecindario', 'Playa del Inglés'],
      stopsVuelta: ['Playa del Inglés', 'Vecindario', 'Telde Intercambiador', 'Jinámar', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('playaIngles', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('playaIngles', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 50 Global: Las Palmas - Faro de Maspalomas (superfaro)
    { 
      line: '50', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Faro de Maspalomas', 
      stopsIda: ['Teatro', 'San Telmo', 'Jinámar', 'Telde', 'Ingenio', 'Agüimes', 'Vecindario', 'San Agustín', 'Playa del Inglés'],
      stopsVuelta: ['Playa del Inglés', 'San Agustín', 'Vecindario', 'Agüimes', 'Ingenio', 'Telde', 'Jinámar', 'San Telmo', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('telde', 'ida'),
        getParada('ingenio', 'ida'),
        getParada('aguimes', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('sanAgustin', 'ida'),
        getParada('playaIngles', 'ida'),
        getParada('maspalomas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('maspalomas', 'vuelta'),
        getParada('playaIngles', 'vuelta'),
        getParada('sanAgustin', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('aguimes', 'vuelta'),
        getParada('ingenio', 'vuelta'),
        getParada('telde', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 32 Global: Playa del Inglés - Puerto de Mogán
    { 
      line: '32', type: 'interurban', company: 'global', origin: 'Playa del Inglés', destination: 'Puerto de Mogán', 
      stopsIda: ['Teatro', 'San Telmo', 'Jinámar', 'Telde Intercambiador', 'Ingenio', 'Carrizal', 'Vecindario'],
      stopsVuelta: ['Vecindario', 'Carrizal', 'Ingenio', 'Telde Intercambiador', 'Jinámar', 'San Telmo', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('ingenio', 'ida'),
        getParada('carrizal', 'ida'),
        getParada('vecindario', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('vecindario', 'vuelta'),
        getParada('carrizal', 'vuelta'),
        getParada('ingenio', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 41 Global: Carrizal - Faro de Maspalomas
    { 
      line: '41', type: 'interurban', company: 'global', origin: 'Carrizal', destination: 'Faro de Maspalomas', 
      stopsIda: ['Teatro', 'San Telmo', 'Jinámar', 'Telde Intercambiador', 'Salinetas', 'Melenara', 'Cruce de Arinaga', 'Arinaga'],
      stopsVuelta: ['Arinaga', 'Cruce de Arinaga', 'Melenara', 'Salinetas', 'Telde Intercambiador', 'Jinámar', 'San Telmo', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('salinetas', 'ida'),
        getParada('melenara', 'ida'),
        getParada('cruce_de_arinaga', 'ida'),
        getParada('arinaga', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('arinaga', 'vuelta'),
        getParada('cruce_de_arinaga', 'vuelta'),
        getParada('melenara', 'vuelta'),
        getParada('salinetas', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 45 Global: Bahía Feliz - Palmitos Park
    { 
      line: '45', type: 'interurban', company: 'global', origin: 'Bahía Feliz', destination: 'Palmitos Park', 
      stopsIda: ['Teatro', 'San Telmo', 'Jinámar', 'Telde Intercambiador', 'Ingenio', 'Agüimes'],
      stopsVuelta: ['Agüimes', 'Ingenio', 'Telde Intercambiador', 'Jinámar', 'San Telmo', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('teldeIntercambiador', 'ida'),
        getParada('ingenio', 'ida'),
        getParada('aguimes', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('aguimes', 'vuelta'),
        getParada('ingenio', 'vuelta'),
        getParada('teldeIntercambiador', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 66 Global: Aeropuerto - Faro de Maspalomas
    { 
      line: '66', type: 'interurban', company: 'global', origin: 'Aeropuerto', destination: 'Faro de Maspalomas', 
      stopsIda: ['Vecindario', 'San Agustín', 'Playa del Inglés', 'Maspalomas'],
      stopsVuelta: ['Maspalomas', 'Playa del Inglés', 'San Agustín', 'Vecindario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('aeropuerto', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('sanAgustin', 'ida'),
        getParada('playaIngles', 'ida'),
        getParada('maspalomas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('maspalomas', 'vuelta'),
        getParada('playaIngles', 'vuelta'),
        getParada('sanAgustin', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('aeropuerto', 'vuelta')
      ]
    },
    
    // Línea 90 Global: Telde - Faro de Maspalomas (semidirecto)
    { 
      line: '90', type: 'interurban', company: 'global', origin: 'Telde', destination: 'Faro de Maspalomas', 
      stopsIda: ['Teatro', 'Vecindario', 'Playa del Inglés'],
      stopsVuelta: ['Playa del Inglés', 'Vecindario', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('playaIngles', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('playaIngles', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 105 Global: Las Palmas - Gáldar (por costa)
    { 
      line: '105', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Gáldar', 
      stopsIda: ['Alcaravaneras', 'Las Arenas', 'Arucas', 'San Felipe', 'Guía', 'Gáldar'],
      stopsVuelta: ['Gáldar', 'Guía', 'San Felipe', 'Arucas', 'Las Arenas', 'San Telmo'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('sanTelmo', 'ida'),
        getParada('alcaravaneras', 'ida'),
        getParada('arucas', 'ida'),
        getParada('guia', 'ida'),
        getParada('galdar', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('galdar', 'vuelta'),
        getParada('guia', 'vuelta'),
        getParada('arucas', 'vuelta'),
        getParada('alcaravaneras', 'vuelta'),
        getParada('sanTelmo', 'vuelta')
      ]
    },
    
    // Línea 4 Global: Las Palmas - Tablero de Maspalomas
    { 
      line: '4', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Tablero de Maspalomas', 
      stopsIda: ['Teatro', 'Telde', 'Vecindario', 'San Fernando', 'Tablero Maspalomas'],
      stopsVuelta: ['Tablero Maspalomas', 'San Fernando', 'Vecindario', 'Telde', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('telde', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('sanFernando', 'ida'),
        getParada('tableroMaspalomas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tableroMaspalomas', 'vuelta'),
        getParada('sanFernando', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('telde', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 6 Global: Cercado de Espino - Arguineguín
    { 
      line: '6', type: 'interurban', company: 'global', origin: 'Cercado de Espino', destination: 'Arguineguín', 
      stopsIda: ['San Fernando', 'Playa del Inglés', 'Arguineguín'],
      stopsVuelta: ['Arguineguín', 'Playa del Inglés', 'San Fernando'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('maspalomas', 'ida'),
        getParada('sanFernando', 'ida'),
        getParada('playaIngles', 'ida'),
        getParada('arguineguin', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('arguineguin', 'vuelta'),
        getParada('playaIngles', 'vuelta'),
        getParada('sanFernando', 'vuelta'),
        getParada('maspalomas', 'vuelta')
      ]
    },
    
    // Línea 9 Global: Cruce de Arinaga - Taurito
    { 
      line: '9', type: 'interurban', company: 'global', origin: 'Cruce de Arinaga', destination: 'Taurito', 
      stopsIda: ['Vecindario', 'San Agustín', 'Maspalomas', 'Puerto Rico', 'Taurito'],
      stopsVuelta: ['Taurito', 'Puerto Rico', 'Maspalomas', 'San Agustín', 'Vecindario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('cruce_de_arinaga', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('sanAgustin', 'ida'),
        getParada('maspalomas', 'ida'),
        getParada('puertoRico', 'ida'),
        getParada('taurito', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('taurito', 'vuelta'),
        getParada('puertoRico', 'vuelta'),
        getParada('maspalomas', 'vuelta'),
        getParada('sanAgustin', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('cruce_de_arinaga', 'vuelta')
      ]
    },
    
    // Línea 19 Global: Telde - Doctoral
    { 
      line: '19', type: 'interurban', company: 'global', origin: 'Telde', destination: 'Doctoral', 
      stopsIda: ['Carrizal', 'Agüimes', 'Doctoral'],
      stopsVuelta: ['Doctoral', 'Agüimes', 'Carrizal'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('telde', 'ida'),
        getParada('carrizal', 'ida'),
        getParada('aguimes', 'ida'),
        getParada('doctoral', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('doctoral', 'vuelta'),
        getParada('aguimes', 'vuelta'),
        getParada('carrizal', 'vuelta'),
        getParada('telde', 'vuelta')
      ]
    },
    
    // Línea 20 Global: Telde - La Herradura
    { 
      line: '20', type: 'interurban', company: 'global', origin: 'Telde', destination: 'La Herradura', 
      stopsIda: ['Jinámar', 'Las Remudas', 'La Herradura'],
      stopsVuelta: ['La Herradura', 'Las Remudas', 'Jinámar'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('telde', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('lasRemudas', 'ida'),
        getParada('laHerradura', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('laHerradura', 'vuelta'),
        getParada('lasRemudas', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('telde', 'vuelta')
      ]
    },
    
    // Línea 22 Global: Agüimes - Playa de Arinaga
    { 
      line: '22', type: 'interurban', company: 'global', origin: 'Agüimes', destination: 'Playa Arinaga', 
      stopsIda: ['Cruce Arinaga', 'Playa Arinaga'],
      stopsVuelta: ['Playa Arinaga', 'Cruce Arinaga'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('aguimes', 'ida'),
        getParada('cruce_de_arinaga', 'ida'),
        getParada('playaArinaga', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('playaArinaga', 'vuelta'),
        getParada('cruce_de_arinaga', 'vuelta'),
        getParada('aguimes', 'vuelta')
      ]
    },
    
    // Línea 23 Global: Las Palmas - Playa de Arinaga (semidirecto)
    { 
      line: '23', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Playa Arinaga', 
      stopsIda: ['Teatro', 'Telde', 'Agüimes', 'Playa Arinaga'],
      stopsVuelta: ['Playa Arinaga', 'Agüimes', 'Telde', 'Teatro'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('teatro', 'ida'),
        getParada('telde', 'ida'),
        getParada('aguimes', 'ida'),
        getParada('playaArinaga', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('playaArinaga', 'vuelta'),
        getParada('aguimes', 'vuelta'),
        getParada('telde', 'vuelta'),
        getParada('teatro', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 33 Global: Playa del Inglés - Puerto de Mogán
    { 
      line: '33', type: 'interurban', company: 'global', origin: 'Playa del Inglés', destination: 'Puerto de Mogán', 
      stopsIda: ['Maspalomas', 'Puerto Rico', 'Puerto de Mogán'],
      stopsVuelta: ['Puerto de Mogán', 'Puerto Rico', 'Maspalomas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('playaIngles', 'ida'),
        getParada('maspalomas', 'ida'),
        getParada('puertoRico', 'ida'),
        getParada('puertoMogan', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('puertoMogan', 'vuelta'),
        getParada('puertoRico', 'vuelta'),
        getParada('maspalomas', 'vuelta'),
        getParada('playaIngles', 'vuelta')
      ]
    },
    
    // Línea 35 Global: Telde - Agüimes
    { 
      line: '35', type: 'interurban', company: 'global', origin: 'Telde', destination: 'Agüimes', 
      stopsIda: ['Carrizal', 'Ingenio', 'Agüimes'],
      stopsVuelta: ['Agüimes', 'Ingenio', 'Carrizal'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('telde', 'ida'),
        getParada('carrizal', 'ida'),
        getParada('ingenio', 'ida'),
        getParada('aguimes', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('aguimes', 'vuelta'),
        getParada('ingenio', 'vuelta'),
        getParada('carrizal', 'vuelta'),
        getParada('telde', 'vuelta')
      ]
    },
    
    // Línea 36 Global: Telde - Faro de Maspalomas
    { 
      line: '36', type: 'interurban', company: 'global', origin: 'Telde', destination: 'Faro de Maspalomas', 
      stopsIda: ['Vecindario', 'San Agustín', 'Playa del Inglés', 'Maspalomas'],
      stopsVuelta: ['Maspalomas', 'Playa del Inglés', 'San Agustín', 'Vecindario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('telde', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('sanAgustin', 'ida'),
        getParada('playaIngles', 'ida'),
        getParada('maspalomas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('maspalomas', 'vuelta'),
        getParada('playaIngles', 'vuelta'),
        getParada('sanAgustin', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('telde', 'vuelta')
      ]
    },
    
    // Línea 39 Global: Playa del Inglés - Playa del Cura
    { 
      line: '39', type: 'interurban', company: 'global', origin: 'Playa del Inglés', destination: 'Playa del Cura', 
      stopsIda: ['Maspalomas', 'Puerto Rico', 'Playa del Cura'],
      stopsVuelta: ['Playa del Cura', 'Puerto Rico', 'Maspalomas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('playaIngles', 'ida'),
        getParada('maspalomas', 'ida'),
        getParada('puertoRico', 'ida'),
        getParada('playaCura', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('playaCura', 'vuelta'),
        getParada('puertoRico', 'vuelta'),
        getParada('maspalomas', 'vuelta'),
        getParada('playaIngles', 'vuelta')
      ]
    },
    
    // Línea 40 Global: Playa del Inglés - Tablero de Maspalomas
    { 
      line: '40', type: 'interurban', company: 'global', origin: 'Playa del Inglés', destination: 'Tablero de Maspalomas', 
      stopsIda: ['San Fernando', 'Tablero Maspalomas'],
      stopsVuelta: ['Tablero Maspalomas', 'San Fernando'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('playaIngles', 'ida'),
        getParada('sanFernando', 'ida'),
        getParada('tableroMaspalomas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tableroMaspalomas', 'vuelta'),
        getParada('sanFernando', 'vuelta'),
        getParada('playaIngles', 'vuelta')
      ]
    },
    
    // Línea 70 Global: Palmitos Park - Puerto de Mogán
    { 
      line: '70', type: 'interurban', company: 'global', origin: 'Palmitos Park', destination: 'Puerto de Mogán', 
      stopsIda: ['Maspalomas', 'Puerto Rico', 'Puerto de Mogán'],
      stopsVuelta: ['Puerto de Mogán', 'Puerto Rico', 'Maspalomas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('palmitosPark', 'ida'),
        getParada('maspalomas', 'ida'),
        getParada('puertoRico', 'ida'),
        getParada('puertoMogan', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('puertoMogan', 'vuelta'),
        getParada('puertoRico', 'vuelta'),
        getParada('maspalomas', 'vuelta'),
        getParada('palmitosPark', 'vuelta')
      ]
    },
    
    // Línea 73 Global: San Fernando - Faro de Maspalomas
    { 
      line: '73', type: 'interurban', company: 'global', origin: 'San Fernando', destination: 'Faro de Maspalomas', 
      stopsIda: ['Tablero Maspalomas', 'Playa del Inglés', 'Maspalomas'],
      stopsVuelta: ['Maspalomas', 'Playa del Inglés', 'Tablero Maspalomas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('sanFernando', 'ida'),
        getParada('tableroMaspalomas', 'ida'),
        getParada('playaIngles', 'ida'),
        getParada('maspalomas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('maspalomas', 'vuelta'),
        getParada('playaIngles', 'vuelta'),
        getParada('tableroMaspalomas', 'vuelta'),
        getParada('sanFernando', 'vuelta')
      ]
    },
    
    // Línea 100 Global: Las Palmas - Gáldar (directo)
    { 
      line: '100', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Gáldar', 
      stopsIda: ['Arucas', 'Guía', 'Gáldar'],
      stopsVuelta: ['Gáldar', 'Guía', 'Arucas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('arucas', 'ida'),
        getParada('guia', 'ida'),
        getParada('galdar', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('galdar', 'vuelta'),
        getParada('guia', 'vuelta'),
        getParada('arucas', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 103 Global: Las Palmas - Puerto de Las Nieves
    { 
      line: '103', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Puerto de las Nieves', 
      stopsIda: ['Arucas', 'Gáldar', 'Agaete', 'Puerto de las Nieves'],
      stopsVuelta: ['Puerto de las Nieves', 'Agaete', 'Gáldar', 'Arucas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('arucas', 'ida'),
        getParada('galdar', 'ida'),
        getParada('agaete', 'ida'),
        getParada('puertoNieves', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('puertoNieves', 'vuelta'),
        getParada('agaete', 'vuelta'),
        getParada('galdar', 'vuelta'),
        getParada('arucas', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 205 Global: Las Palmas - Tamaraceite - Arucas
    { 
      line: '205', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Arucas', 
      stopsIda: ['Tamaraceite', 'Arucas'],
      stopsVuelta: ['Arucas', 'Tamaraceite'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('tamaraceite', 'ida'),
        getParada('arucas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('arucas', 'vuelta'),
        getParada('tamaraceite', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 210 Global: Las Palmas - Cardones - Arucas
    { 
      line: '210', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Arucas', 
      stopsIda: ['Bañaderos', 'Cardones', 'Arucas'],
      stopsVuelta: ['Arucas', 'Cardones', 'Bañaderos'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('bananeros', 'ida'),
        getParada('cardones', 'ida'),
        getParada('arucas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('arucas', 'vuelta'),
        getParada('cardones', 'vuelta'),
        getParada('bananeros', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 220 Global: Las Palmas - Teror - Artenara
    { 
      line: '220', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Artenara', 
      stopsIda: ['Tamaraceite', 'Teror', 'Artenara'],
      stopsVuelta: ['Artenara', 'Teror', 'Tamaraceite'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('tamaraceite', 'ida'),
        getParada('teror', 'ida'),
        getParada('artenara', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('artenara', 'vuelta'),
        getParada('teror', 'vuelta'),
        getParada('tamaraceite', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 301 Global: Las Palmas - Santa Brígida
    { 
      line: '301', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Santa Brígida', 
      stopsIda: ['Campus Universitario', 'Tafira', 'Santa Brígida'],
      stopsVuelta: ['Santa Brígida', 'Tafira', 'Campus Universitario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('campusULPGC', 'ida'),
        getParada('tafiraAlta', 'ida'),
        getParada('santaBrigida', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('santaBrigida', 'vuelta'),
        getParada('tafiraAlta', 'vuelta'),
        getParada('campusULPGC', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 303 Global: Las Palmas - San Mateo
    { 
      line: '303', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'San Mateo', 
      stopsIda: ['Campus Universitario', 'Santa Brígida', 'San Mateo'],
      stopsVuelta: ['San Mateo', 'Santa Brígida', 'Campus Universitario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('campusULPGC', 'ida'),
        getParada('santaBrigida', 'ida'),
        getParada('sanMateo', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sanMateo', 'vuelta'),
        getParada('santaBrigida', 'vuelta'),
        getParada('campusULPGC', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 305 Global: San Mateo - Tejeda
    { 
      line: '305', type: 'interurban', company: 'global', origin: 'San Mateo', destination: 'Tejeda', 
      stopsIda: ['Tejeda'],
      stopsVuelta: ['Tejeda'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('sanMateo', 'ida'),
        getParada('tejeda', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tejeda', 'vuelta'),
        getParada('sanMateo', 'vuelta')
      ]
    },
    
    // Línea 55 Global: Las Palmas - Valle de Jinámar
    { 
      line: '55', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Valle de Jinámar', 
      stopsIda: ['Jinámar', 'Valle de Jinámar'],
      stopsVuelta: ['Valle de Jinámar', 'Jinámar'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('valleJinamar', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('valleJinamar', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 57 Global: Las Palmas - La Montañeta
    { 
      line: '57', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'La Montañeta', 
      stopsIda: ['Jinámar', 'La Montañeta'],
      stopsVuelta: ['La Montañeta', 'Jinámar'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('montaneta', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('montaneta', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 116 Global: Las Palmas - Moya
    { 
      line: '116', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Moya', 
      stopsIda: ['Arucas', 'Cabo Verde', 'Moya'],
      stopsVuelta: ['Moya', 'Cabo Verde', 'Arucas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('arucas', 'ida'),
        getParada('caboVerde', 'ida'),
        getParada('moya', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('moya', 'vuelta'),
        getParada('caboVerde', 'vuelta'),
        getParada('arucas', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 204 Global: Las Palmas - Firgas
    { 
      line: '204', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Firgas', 
      stopsIda: ['Arucas', 'Firgas'],
      stopsVuelta: ['Firgas', 'Arucas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('arucas', 'ida'),
        getParada('firgas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('firgas', 'vuelta'),
        getParada('arucas', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 8 Global: Las Palmas Santa Catalina - Doctoral (semidirecto)
    { 
      line: '8', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Doctoral', 
      stopsIda: ['Telde', 'Ingenio', 'Agüimes', 'Doctoral'],
      stopsVuelta: ['Doctoral', 'Agüimes', 'Ingenio', 'Telde'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('telde', 'ida'),
        getParada('ingenio', 'ida'),
        getParada('aguimes', 'ida'),
        getParada('doctoral', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('doctoral', 'vuelta'),
        getParada('aguimes', 'vuelta'),
        getParada('ingenio', 'vuelta'),
        getParada('telde', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 13 Global: Telde - Tenteniguada
    { 
      line: '13', type: 'interurban', company: 'global', origin: 'Telde', destination: 'Tenteniguada', 
      stopsIda: ['Valsequillo', 'Tenteniguada'],
      stopsVuelta: ['Tenteniguada', 'Valsequillo'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('telde', 'ida'),
        getParada('valsequillo', 'ida'),
        getParada('tenteniguada', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tenteniguada', 'vuelta'),
        getParada('valsequillo', 'vuelta'),
        getParada('telde', 'vuelta')
      ]
    },
    
    // Línea 24 Global: Telde - Santa Brígida
    { 
      line: '24', type: 'interurban', company: 'global', origin: 'Telde', destination: 'Santa Brígida', 
      stopsIda: ['Valsequillo', 'Santa Brígida'],
      stopsVuelta: ['Santa Brígida', 'Valsequillo'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('telde', 'ida'),
        getParada('valsequillo', 'ida'),
        getParada('santaBrigida', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('santaBrigida', 'vuelta'),
        getParada('valsequillo', 'vuelta'),
        getParada('telde', 'vuelta')
      ]
    },
    
    // Línea 43 Global: Telde - Valsequillo
    { 
      line: '43', type: 'interurban', company: 'global', origin: 'Telde', destination: 'Valsequillo', 
      stopsIda: ['San Roque', 'Valsequillo'],
      stopsVuelta: ['Valsequillo', 'San Roque'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('telde', 'ida'),
        getParada('tafiraBaja', 'ida'),
        getParada('valsequillo', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('valsequillo', 'vuelta'),
        getParada('tafiraBaja', 'vuelta'),
        getParada('telde', 'vuelta')
      ]
    },
    
    // Línea 52 Global: Castillo del Romeral - Faro de Maspalomas
    { 
      line: '52', type: 'interurban', company: 'global', origin: 'Castillo del Romeral', destination: 'Faro de Maspalomas', 
      stopsIda: ['Vecindario', 'San Agustín', 'Playa del Inglés', 'Maspalomas'],
      stopsVuelta: ['Maspalomas', 'Playa del Inglés', 'San Agustín', 'Vecindario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('castilloRomeral', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('sanAgustin', 'ida'),
        getParada('playaIngles', 'ida'),
        getParada('maspalomas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('maspalomas', 'vuelta'),
        getParada('playaIngles', 'vuelta'),
        getParada('sanAgustin', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('castilloRomeral', 'vuelta')
      ]
    },
    
    // Línea 58 Global: Las Palmas - Tafira
    { 
      line: '58', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Tafira', 
      stopsIda: ['Campus Universitario', 'Tafira Alta'],
      stopsVuelta: ['Tafira Alta', 'Campus Universitario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('campusULPGC', 'ida'),
        getParada('tafiraAlta', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tafiraAlta', 'vuelta'),
        getParada('campusULPGC', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 74 Global: Las Palmas - Eucalipto 2
    { 
      line: '74', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Eucaliptos 2', 
      stopsIda: ['Jinámar', 'Valle Jinámar', 'Eucaliptos 2'],
      stopsVuelta: ['Eucaliptos 2', 'Valle Jinámar', 'Jinámar'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('valleJinamar', 'ida'),
        getParada('eucaliptos2', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('eucaliptos2', 'vuelta'),
        getParada('valleJinamar', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 75 Global: Las Palmas - Vial Costero de Telde
    { 
      line: '75', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Vial Costero Telde', 
      stopsIda: ['Telde', 'Melenara', 'Vial Costero'],
      stopsVuelta: ['Vial Costero', 'Melenara', 'Telde'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('telde', 'ida'),
        getParada('melenara', 'ida'),
        getParada('vialCosteroTelde', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('vialCosteroTelde', 'vuelta'),
        getParada('melenara', 'vuelta'),
        getParada('telde', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 84 Global: Puerto de Mogán - Mogán
    { 
      line: '84', type: 'interurban', company: 'global', origin: 'Puerto de Mogán', destination: 'Mogán', 
      stopsIda: ['Arguineguín', 'Puerto Rico'],
      stopsVuelta: ['Puerto Rico', 'Arguineguín'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('puertoMogan', 'ida'),
        getParada('arguineguin', 'ida'),
        getParada('puertoRico', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('puertoRico', 'vuelta'),
        getParada('arguineguin', 'vuelta'),
        getParada('puertoMogan', 'vuelta')
      ]
    },
    
    // Línea 216 Global: Las Palmas - El Toscón - Teror
    { 
      line: '216', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Teror', 
      stopsIda: ['Tamaraceite', 'El Toscón', 'Teror'],
      stopsVuelta: ['Teror', 'El Toscón', 'Tamaraceite'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('tamaraceite', 'ida'),
        getParada('elToscon', 'ida'),
        getParada('teror', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('teror', 'vuelta'),
        getParada('elToscon', 'vuelta'),
        getParada('tamaraceite', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 233 Global: Santa Catalina - Tenoya Alta
    { 
      line: '233', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Tenoya Alta', 
      stopsIda: ['Tamaraceite', 'Tenoya', 'Tenoya Alta'],
      stopsVuelta: ['Tenoya Alta', 'Tenoya', 'Tamaraceite'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('tamaraceite', 'ida'),
        getParada('tenoya', 'ida'),
        getParada('tenoyaAlta', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tenoyaAlta', 'vuelta'),
        getParada('tenoya', 'vuelta'),
        getParada('tamaraceite', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 234 Global: Las Palmas - Arucas (directo)
    { 
      line: '234', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Arucas', 
      stopsIda: ['Arucas'],
      stopsVuelta: ['Arucas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('arucas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('arucas', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 316 Global: Las Palmas - Cementerio San Lázaro
    { 
      line: '316', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Cementerio San Lázaro', 
      stopsIda: ['Campus Universitario', 'Cementerio San Lázaro'],
      stopsVuelta: ['Cementerio San Lázaro', 'Campus Universitario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('campusULPGC', 'ida'),
        getParada('cementerioSanLazaro', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('cementerioSanLazaro', 'vuelta'),
        getParada('campusULPGC', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 321 Global: Campus Universitario - Puerto de Mogán
    { 
      line: '321', type: 'interurban', company: 'global', origin: 'Campus Universitario', destination: 'Puerto de Mogán', 
      stopsIda: ['Vecindario', 'Maspalomas', 'Puerto Rico', 'Puerto de Mogán'],
      stopsVuelta: ['Puerto de Mogán', 'Puerto Rico', 'Maspalomas', 'Vecindario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('campusULPGC', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('maspalomas', 'ida'),
        getParada('puertoRico', 'ida'),
        getParada('puertoMogan', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('puertoMogan', 'vuelta'),
        getParada('puertoRico', 'vuelta'),
        getParada('maspalomas', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('campusULPGC', 'vuelta')
      ]
    },
    
    // Línea 326 Global: Tamaraceite - Campus Universitario
    { 
      line: '326', type: 'interurban', company: 'global', origin: 'Tamaraceite', destination: 'Campus Universitario', 
      stopsIda: ['Siete Palmas', 'Campus Universitario'],
      stopsVuelta: ['Campus Universitario', 'Siete Palmas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('tamaraceite', 'ida'),
        getParada('sietePalmas', 'ida'),
        getParada('campusULPGC', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('campusULPGC', 'vuelta'),
        getParada('sietePalmas', 'vuelta'),
        getParada('tamaraceite', 'vuelta')
      ]
    },
    
    // Línea 327 Global: Las Palmas - Lomo Blanco
    { 
      line: '327', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Lomo Blanco', 
      stopsIda: ['Campus Universitario', 'Lomo Blanco'],
      stopsVuelta: ['Lomo Blanco', 'Campus Universitario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('campusULPGC', 'ida'),
        getParada('lomoBlanco', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lomoBlanco', 'vuelta'),
        getParada('campusULPGC', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 331 Global: Campus Universitario - Teror
    { 
      line: '331', type: 'interurban', company: 'global', origin: 'Campus Universitario', destination: 'Teror', 
      stopsIda: ['Tamaraceite', 'Teror'],
      stopsVuelta: ['Teror', 'Tamaraceite'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('campusULPGC', 'ida'),
        getParada('tamaraceite', 'ida'),
        getParada('teror', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('teror', 'vuelta'),
        getParada('tamaraceite', 'vuelta'),
        getParada('campusULPGC', 'vuelta')
      ]
    },
    
    // Línea 332 Global: Campus Universitario - Telde
    { 
      line: '332', type: 'interurban', company: 'global', origin: 'Campus Universitario', destination: 'Telde', 
      stopsIda: ['Telde'],
      stopsVuelta: ['Telde'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('campusULPGC', 'ida'),
        getParada('telde', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('telde', 'vuelta'),
        getParada('campusULPGC', 'vuelta')
      ]
    },
    
    // Línea 335 Global: Las Palmas - San Lorenzo - Tamaraceite
    { 
      line: '335', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Tamaraceite', 
      stopsIda: ['San Lorenzo', 'Tamaraceite'],
      stopsVuelta: ['Tamaraceite', 'San Lorenzo'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('sanLorenzo', 'ida'),
        getParada('tamaraceite', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tamaraceite', 'vuelta'),
        getParada('sanLorenzo', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 350 Global: Las Palmas - Polígono de Arinaga
    { 
      line: '350', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Polígono de Arinaga', 
      stopsIda: ['Telde', 'Agüimes', 'Arinaga'],
      stopsVuelta: ['Arinaga', 'Agüimes', 'Telde'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('telde', 'ida'),
        getParada('aguimes', 'ida'),
        getParada('arinaga', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('arinaga', 'vuelta'),
        getParada('aguimes', 'vuelta'),
        getParada('telde', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 101 Global: Gáldar - San Nicolás de Tolentino
    { 
      line: '101', type: 'interurban', company: 'global', origin: 'Gáldar', destination: 'San Nicolás', 
      stopsIda: ['Agaete', 'Aldea San Nicolás'],
      stopsVuelta: ['Aldea San Nicolás', 'Agaete'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('galdar', 'ida'),
        getParada('agaete', 'ida'),
        getParada('aldeaSanNicolas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('aldeaSanNicolas', 'vuelta'),
        getParada('agaete', 'vuelta'),
        getParada('galdar', 'vuelta')
      ]
    },
    
    // Línea 127 Global: Moya - Fontanales
    { 
      line: '127', type: 'interurban', company: 'global', origin: 'Moya', destination: 'Fontanales', 
      stopsIda: ['Los Dragos', 'Fontanales'],
      stopsVuelta: ['Fontanales', 'Los Dragos'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('moya', 'ida'),
        getParada('losDragos', 'ida'),
        getParada('fontanales', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('fontanales', 'vuelta'),
        getParada('losDragos', 'vuelta'),
        getParada('moya', 'vuelta')
      ]
    },
    
    // Línea 130 Global: Las Palmas - Puerto de Las Nieves (Semidirecto)
    { 
      line: '130', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Puerto de Las Nieves', 
      stopsIda: ['Gáldar', 'Agaete', 'Puerto de Las Nieves'],
      stopsVuelta: ['Puerto de Las Nieves', 'Agaete', 'Gáldar'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('galdar', 'ida'),
        getParada('agaete', 'ida'),
        getParada('puertoNieves', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('puertoNieves', 'vuelta'),
        getParada('agaete', 'vuelta'),
        getParada('galdar', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 206 Global: Las Palmas - Bañaderos - Arucas
    { 
      line: '206', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Arucas', 
      stopsIda: ['Bañaderos', 'Arucas'],
      stopsVuelta: ['Arucas', 'Bañaderos'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('bananeros', 'ida'),
        getParada('arucas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('arucas', 'vuelta'),
        getParada('bananeros', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 211 Global: Arucas - Firgas
    { 
      line: '211', type: 'interurban', company: 'global', origin: 'Arucas', destination: 'Firgas', 
      stopsIda: ['Firgas'],
      stopsVuelta: ['Firgas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('arucas', 'ida'),
        getParada('firgas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('firgas', 'vuelta'),
        getParada('arucas', 'vuelta')
      ]
    },
    
    // Línea 214 Global: Teror - San Mateo
    { 
      line: '214', type: 'interurban', company: 'global', origin: 'Teror', destination: 'San Mateo', 
      stopsIda: ['San Mateo'],
      stopsVuelta: ['San Mateo'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('teror', 'ida'),
        getParada('sanMateo', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sanMateo', 'vuelta'),
        getParada('teror', 'vuelta')
      ]
    },
    
    // Línea 302 Global: Las Palmas - La Calzada - Santa Brígida
    { 
      line: '302', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Santa Brígida', 
      stopsIda: ['Campus Universitario', 'Tafira Baja', 'Santa Brígida'],
      stopsVuelta: ['Santa Brígida', 'Tafira Baja', 'Campus Universitario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('campusULPGC', 'ida'),
        getParada('tafiraBaja', 'ida'),
        getParada('santaBrigida', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('santaBrigida', 'vuelta'),
        getParada('tafiraBaja', 'vuelta'),
        getParada('campusULPGC', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 308 Global: Santa Brígida - San Mateo
    { 
      line: '308', type: 'interurban', company: 'global', origin: 'Santa Brígida', destination: 'San Mateo', 
      stopsIda: ['San Mateo'],
      stopsVuelta: ['San Mateo'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaBrigida', 'ida'),
        getParada('sanMateo', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sanMateo', 'vuelta'),
        getParada('santaBrigida', 'vuelta')
      ]
    },
    
    // Línea 311 Global: Las Palmas - Santa Brígida
    { 
      line: '311', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Santa Brígida', 
      stopsIda: ['Tafira Alta', 'Santa Brígida'],
      stopsVuelta: ['Santa Brígida', 'Tafira Alta'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('tafiraAlta', 'ida'),
        getParada('santaBrigida', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('santaBrigida', 'vuelta'),
        getParada('tafiraAlta', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 313 Global: Las Palmas - El Fondillo
    { 
      line: '313', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'El Fondillo', 
      stopsIda: ['Campus Universitario', 'El Fondillo'],
      stopsVuelta: ['El Fondillo', 'Campus Universitario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('campusULPGC', 'ida'),
        getParada('fondillo', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('fondillo', 'vuelta'),
        getParada('campusULPGC', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 320 Global: Las Palmas - Ciudad del Campo Alto
    { 
      line: '320', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Ciudad del Campo Alto', 
      stopsIda: ['Campus Universitario', 'Ciudad del Campo Alto'],
      stopsVuelta: ['Ciudad del Campo Alto', 'Campus Universitario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('campusULPGC', 'ida'),
        getParada('ciudadCampoAlto', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('ciudadCampoAlto', 'vuelta'),
        getParada('campusULPGC', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 322 Global: Campus Universitario - Doctoral
    { 
      line: '322', type: 'interurban', company: 'global', origin: 'Campus Universitario', destination: 'Doctoral', 
      stopsIda: ['Vecindario', 'Doctoral'],
      stopsVuelta: ['Doctoral', 'Vecindario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('campusULPGC', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('doctoral', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('doctoral', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('campusULPGC', 'vuelta')
      ]
    },
    
    // Línea 323 Global: Las Palmas Santa Catalina - San Mateo
    { 
      line: '323', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'San Mateo', 
      stopsIda: ['Santa Brígida', 'San Mateo'],
      stopsVuelta: ['San Mateo', 'Santa Brígida'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('santaBrigida', 'ida'),
        getParada('sanMateo', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sanMateo', 'vuelta'),
        getParada('santaBrigida', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 324 Global: Campus Universitario - Hospital Dr. Negrín - Arucas
    { 
      line: '324', type: 'interurban', company: 'global', origin: 'Campus Universitario', destination: 'Arucas', 
      stopsIda: ['Hospital Negrín', 'Arucas'],
      stopsVuelta: ['Arucas', 'Hospital Negrín'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('campusULPGC', 'ida'),
        getParada('hospitalNegrin', 'ida'),
        getParada('arucas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('arucas', 'vuelta'),
        getParada('hospitalNegrin', 'vuelta'),
        getParada('campusULPGC', 'vuelta')
      ]
    },
    
    // Línea 325 Global: Campus Universitario - Hospital Dr. Negrín - Gáldar
    { 
      line: '325', type: 'interurban', company: 'global', origin: 'Campus Universitario', destination: 'Gáldar', 
      stopsIda: ['Hospital Negrín', 'Arucas', 'Gáldar'],
      stopsVuelta: ['Gáldar', 'Arucas', 'Hospital Negrín'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('campusULPGC', 'ida'),
        getParada('hospitalNegrin', 'ida'),
        getParada('arucas', 'ida'),
        getParada('galdar', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('galdar', 'vuelta'),
        getParada('arucas', 'vuelta'),
        getParada('hospitalNegrin', 'vuelta'),
        getParada('campusULPGC', 'vuelta')
      ]
    },
    
    // Línea 328 Global: Plaza de Manuel Becerra - Campus Universitario
    { 
      line: '328', type: 'interurban', company: 'global', origin: 'Plaza de Manuel Becerra', destination: 'Campus Universitario', 
      stopsIda: ['Santa Catalina', 'Campus Universitario'],
      stopsVuelta: ['Campus Universitario', 'Santa Catalina'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('puerto', 'ida'),
        getParada('santaCatalina', 'ida'),
        getParada('campusULPGC', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('campusULPGC', 'vuelta'),
        getParada('santaCatalina', 'vuelta'),
        getParada('puerto', 'vuelta')
      ]
    },
    
    // Línea 329 Global: Campus Universitario - Agüimes
    { 
      line: '329', type: 'interurban', company: 'global', origin: 'Campus Universitario', destination: 'Agüimes', 
      stopsIda: ['Telde', 'Agüimes'],
      stopsVuelta: ['Agüimes', 'Telde'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('campusULPGC', 'ida'),
        getParada('telde', 'ida'),
        getParada('aguimes', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('aguimes', 'vuelta'),
        getParada('telde', 'vuelta'),
        getParada('campusULPGC', 'vuelta')
      ]
    },
    
    // Línea 333 Global: Campus Universitario - Tablero de Maspalomas
    { 
      line: '333', type: 'interurban', company: 'global', origin: 'Campus Universitario', destination: 'Tablero de Maspalomas', 
      stopsIda: ['Vecindario', 'Maspalomas', 'Tablero de Maspalomas'],
      stopsVuelta: ['Tablero de Maspalomas', 'Maspalomas', 'Vecindario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('campusULPGC', 'ida'),
        getParada('vecindario', 'ida'),
        getParada('maspalomas', 'ida'),
        getParada('tableroMaspalomas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tableroMaspalomas', 'vuelta'),
        getParada('maspalomas', 'vuelta'),
        getParada('vecindario', 'vuelta'),
        getParada('campusULPGC', 'vuelta')
      ]
    },
    
    // Línea 56 Global: Telde - Valle de Jinámar
    { 
      line: '56', type: 'interurban', company: 'global', origin: 'Telde', destination: 'Valle de Jinámar', 
      stopsIda: ['Jinámar', 'Valle Jinámar'],
      stopsVuelta: ['Valle Jinámar', 'Jinámar'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('telde', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('valleJinamar', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('valleJinamar', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('telde', 'vuelta')
      ]
    },
    
    // Línea 59 Global: Las Palmas - Las Ramblas de Jinámar
    { 
      line: '59', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Las Ramblas de Jinámar', 
      stopsIda: ['Jinámar', 'Ramblas Jinámar'],
      stopsVuelta: ['Ramblas Jinámar', 'Jinámar'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('ramblasJinamar', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('ramblasJinamar', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 65 Global: Telde - Eucaliptos 2 - Ramblas de Jinámar
    { 
      line: '65', type: 'interurban', company: 'global', origin: 'Telde', destination: 'Las Ramblas de Jinámar', 
      stopsIda: ['Eucaliptos 2', 'Ramblas Jinámar'],
      stopsVuelta: ['Ramblas Jinámar', 'Eucaliptos 2'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('telde', 'ida'),
        getParada('eucaliptos2', 'ida'),
        getParada('ramblasJinamar', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('ramblasJinamar', 'vuelta'),
        getParada('eucaliptos2', 'vuelta'),
        getParada('telde', 'vuelta')
      ]
    },
    
    // Línea 85 Global: Playa del Burrero - Los Molinillos
    { 
      line: '85', type: 'interurban', company: 'global', origin: 'Playa del Burrero', destination: 'Los Molinillos', 
      stopsIda: ['Melenara', 'Telde'],
      stopsVuelta: ['Telde', 'Melenara'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('playaBurrero', 'ida'),
        getParada('melenara', 'ida'),
        getParada('telde', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('telde', 'vuelta'),
        getParada('melenara', 'vuelta'),
        getParada('playaBurrero', 'vuelta')
      ]
    },
    
    // ========== LÍNEAS GÁLDAR (400s) ==========
    
    // Línea 401 Global: Gáldar - Sardina del Norte
    { 
      line: '401', type: 'interurban', company: 'global', origin: 'Gáldar', destination: 'Sardina del Norte', 
      stopsIda: ['Sardina Norte'],
      stopsVuelta: ['Sardina Norte'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('galdar', 'ida'),
        getParada('sardinaNorte', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sardinaNorte', 'vuelta'),
        getParada('galdar', 'vuelta')
      ]
    },
    
    // Línea 402 Global: Gáldar - Barrial - Sardina del Norte
    { 
      line: '402', type: 'interurban', company: 'global', origin: 'Gáldar', destination: 'Sardina del Norte', 
      stopsIda: ['Barrial', 'Cumbrecillas Faro', 'Sardina Norte'],
      stopsVuelta: ['Sardina Norte', 'Cumbrecillas Faro', 'Barrial'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('galdar', 'ida'),
        getParada('barrial', 'ida'),
        getParada('cumbrecillasFaro', 'ida'),
        getParada('sardinaNorte', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sardinaNorte', 'vuelta'),
        getParada('cumbrecillasFaro', 'vuelta'),
        getParada('barrial', 'vuelta'),
        getParada('galdar', 'vuelta')
      ]
    },
    
    // Línea 403 Global: Gáldar - Dos Roques - La Furnia - Punta de Gáldar
    { 
      line: '403', type: 'interurban', company: 'global', origin: 'Gáldar', destination: 'La Punta de Gáldar', 
      stopsIda: ['Dos Roques', 'La Furnia', 'Punta Gáldar'],
      stopsVuelta: ['Punta Gáldar', 'La Furnia', 'Dos Roques'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('galdar', 'ida'),
        getParada('dosRoques', 'ida'),
        getParada('laFurnia', 'ida'),
        getParada('puntaGaldar', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('puntaGaldar', 'vuelta'),
        getParada('laFurnia', 'vuelta'),
        getParada('dosRoques', 'vuelta'),
        getParada('galdar', 'vuelta')
      ]
    },
    
    // Línea 404 Global: Gáldar - El Roque - Barrial
    { 
      line: '404', type: 'interurban', company: 'global', origin: 'Gáldar', destination: 'Barrial', 
      stopsIda: ['El Roque', 'Barrial'],
      stopsVuelta: ['Barrial', 'El Roque'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('galdar', 'ida'),
        getParada('elRoque', 'ida'),
        getParada('barrial', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('barrial', 'vuelta'),
        getParada('elRoque', 'vuelta'),
        getParada('galdar', 'vuelta')
      ]
    },
    
    // Línea 102 Global: Gáldar - Valle de Agaete
    { 
      line: '102', type: 'interurban', company: 'global', origin: 'Gáldar', destination: 'Valle de Agaete', 
      stopsIda: ['Agaete', 'Valle Agaete'],
      stopsVuelta: ['Valle Agaete', 'Agaete'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('galdar', 'ida'),
        getParada('agaete', 'ida'),
        getParada('valleAgaete', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('valleAgaete', 'vuelta'),
        getParada('agaete', 'vuelta'),
        getParada('galdar', 'vuelta')
      ]
    },
    
    // Línea 106 Global: Gáldar - Fagajesto
    { 
      line: '106', type: 'interurban', company: 'global', origin: 'Gáldar', destination: 'Fagajesto', 
      stopsIda: ['Fagajesto'],
      stopsVuelta: ['Fagajesto'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('galdar', 'ida'),
        getParada('fagajesto', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('fagajesto', 'vuelta'),
        getParada('galdar', 'vuelta')
      ]
    },
    
    // Línea 107 Global: Gáldar - Montaña Alta
    { 
      line: '107', type: 'interurban', company: 'global', origin: 'Gáldar', destination: 'Montaña Alta', 
      stopsIda: ['Montaña Alta'],
      stopsVuelta: ['Montaña Alta'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('galdar', 'ida'),
        getParada('montanaAlta', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('montanaAlta', 'vuelta'),
        getParada('galdar', 'vuelta')
      ]
    },
    
    // Línea 108 Global: Gáldar - Marmolejos
    { 
      line: '108', type: 'interurban', company: 'global', origin: 'Gáldar', destination: 'Marmolejos', 
      stopsIda: ['Marmolejos'],
      stopsVuelta: ['Marmolejos'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('galdar', 'ida'),
        getParada('marmolejos', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('marmolejos', 'vuelta'),
        getParada('galdar', 'vuelta')
      ]
    },
    
    // ========== LÍNEAS ARUCAS (250s) ==========
    
    // Línea 250 Global: Arucas - Santidad
    { 
      line: '250', type: 'interurban', company: 'global', origin: 'Arucas', destination: 'Santidad', 
      stopsIda: ['Santidad'],
      stopsVuelta: ['Santidad'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('arucas', 'ida'),
        getParada('santidad', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('santidad', 'vuelta'),
        getParada('arucas', 'vuelta')
      ]
    },
    
    // Línea 251 Global: Arucas - La Goleta - Cruz de Firgas
    { 
      line: '251', type: 'interurban', company: 'global', origin: 'Arucas', destination: 'Cruz de Firgas', 
      stopsIda: ['La Goleta', 'Cruz Firgas'],
      stopsVuelta: ['Cruz Firgas', 'La Goleta'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('arucas', 'ida'),
        getParada('laGoleta', 'ida'),
        getParada('cruzFirgas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('cruzFirgas', 'vuelta'),
        getParada('laGoleta', 'vuelta'),
        getParada('arucas', 'vuelta')
      ]
    },
    
    // Línea 255 Global: Arucas - Altabacales
    { 
      line: '255', type: 'interurban', company: 'global', origin: 'Arucas', destination: 'Altabacales', 
      stopsIda: ['Altabacales'],
      stopsVuelta: ['Altabacales'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('arucas', 'ida'),
        getParada('altabacales', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('altabacales', 'vuelta'),
        getParada('arucas', 'vuelta')
      ]
    },
    
    // Línea 256 Global: Arucas - Trasmontaña
    { 
      line: '256', type: 'interurban', company: 'global', origin: 'Arucas', destination: 'Trasmontaña', 
      stopsIda: ['Trasmontaña'],
      stopsVuelta: ['Trasmontaña'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('arucas', 'ida'),
        getParada('trasmontana', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('trasmontana', 'vuelta'),
        getParada('arucas', 'vuelta')
      ]
    },
    
    // Línea 213 Global: Arucas - Trapiche - San Felipe
    { 
      line: '213', type: 'interurban', company: 'global', origin: 'Arucas', destination: 'San Felipe', 
      stopsIda: ['Trapiche', 'San Felipe'],
      stopsVuelta: ['San Felipe', 'Trapiche'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('arucas', 'ida'),
        getParada('trapiche', 'ida'),
        getParada('sanFelipe', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sanFelipe', 'vuelta'),
        getParada('trapiche', 'vuelta'),
        getParada('arucas', 'vuelta')
      ]
    },
    
    // Línea 215 Global: Teror - Arucas - San Felipe
    { 
      line: '215', type: 'interurban', company: 'global', origin: 'Teror', destination: 'San Felipe', 
      stopsIda: ['Arucas', 'San Felipe'],
      stopsVuelta: ['San Felipe', 'Arucas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('teror', 'ida'),
        getParada('arucas', 'ida'),
        getParada('sanFelipe', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('sanFelipe', 'vuelta'),
        getParada('arucas', 'vuelta'),
        getParada('teror', 'vuelta')
      ]
    },
    
    // Línea 123 Global: Moya - Arucas
    { 
      line: '123', type: 'interurban', company: 'global', origin: 'Moya', destination: 'Arucas', 
      stopsIda: ['Arucas'],
      stopsVuelta: ['Arucas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('moya', 'ida'),
        getParada('arucas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('arucas', 'vuelta'),
        getParada('moya', 'vuelta')
      ]
    },
    
    // Línea 124 Global: Gáldar - El Palmital - Moya
    { 
      line: '124', type: 'interurban', company: 'global', origin: 'Gáldar', destination: 'Moya', 
      stopsIda: ['El Palmital', 'Moya'],
      stopsVuelta: ['Moya', 'El Palmital'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('galdar', 'ida'),
        getParada('elPalmital', 'ida'),
        getParada('moya', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('moya', 'vuelta'),
        getParada('elPalmital', 'vuelta'),
        getParada('galdar', 'vuelta')
      ]
    },
    
    // Línea 126 Global: Moya - Cruz del Pagador - Gáldar
    { 
      line: '126', type: 'interurban', company: 'global', origin: 'Moya', destination: 'Gáldar', 
      stopsIda: ['Cruz Pagador', 'Gáldar'],
      stopsVuelta: ['Gáldar', 'Cruz Pagador'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('moya', 'ida'),
        getParada('cruzPagador', 'ida'),
        getParada('galdar', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('galdar', 'vuelta'),
        getParada('cruzPagador', 'vuelta'),
        getParada('moya', 'vuelta')
      ]
    },
    
    // ========== LÍNEAS DE MONTAÑA ==========
    
    // Línea 307 Global: San Mateo - Ariñez - Las Lagunetas - Cueva Grande
    { 
      line: '307', type: 'interurban', company: 'global', origin: 'San Mateo', destination: 'Cueva Grande', 
      stopsIda: ['Ariñez', 'Las Lagunetas', 'Cueva Grande'],
      stopsVuelta: ['Cueva Grande', 'Las Lagunetas', 'Ariñez'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('sanMateo', 'ida'),
        getParada('ariñez', 'ida'),
        getParada('lasLagunetas', 'ida'),
        getParada('cuevaGrande', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('cuevaGrande', 'vuelta'),
        getParada('lasLagunetas', 'vuelta'),
        getParada('ariñez', 'vuelta'),
        getParada('sanMateo', 'vuelta')
      ]
    },
    
    // Línea 306 Global: San Mateo - Lomo Carbonero - Santa Brígida
    { 
      line: '306', type: 'interurban', company: 'global', origin: 'San Mateo', destination: 'Santa Brígida', 
      stopsIda: ['Lomo Carbonero', 'Santa Brígida'],
      stopsVuelta: ['Santa Brígida', 'Lomo Carbonero'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('sanMateo', 'ida'),
        getParada('lomoCarbonero', 'ida'),
        getParada('santaBrigida', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('santaBrigida', 'vuelta'),
        getParada('lomoCarbonero', 'vuelta'),
        getParada('sanMateo', 'vuelta')
      ]
    },
    
    // Línea 318 Global: Santa Brígida - Pino Santo
    { 
      line: '318', type: 'interurban', company: 'global', origin: 'Santa Brígida', destination: 'Pino Santo', 
      stopsIda: ['Pino Santo'],
      stopsVuelta: ['Pino Santo'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaBrigida', 'ida'),
        getParada('pinoSanto', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('pinoSanto', 'vuelta'),
        getParada('santaBrigida', 'vuelta')
      ]
    },
    
    // Línea 317 Global: Las Palmas - Los Giles - Santa Catalina
    { 
      line: '317', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Los Giles', 
      stopsIda: ['Campus Universitario', 'Los Giles'],
      stopsVuelta: ['Los Giles', 'Campus Universitario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('campusULPGC', 'ida'),
        getParada('losGiles', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('losGiles', 'vuelta'),
        getParada('campusULPGC', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 319 Global: Santa Catalina - Las Mesas
    { 
      line: '319', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Las Mesas', 
      stopsIda: ['Tamaraceite', 'Las Mesas'],
      stopsVuelta: ['Las Mesas', 'Tamaraceite'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('tamaraceite', 'ida'),
        getParada('lasMesas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lasMesas', 'vuelta'),
        getParada('tamaraceite', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // ========== LÍNEAS ADICIONALES ==========
    
    // Línea 218 Global: Teror - Lanzarote
    { 
      line: '218', type: 'interurban', company: 'global', origin: 'Teror', destination: 'Lanzarote', 
      stopsIda: ['Lanzarote'],
      stopsVuelta: ['Lanzarote'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('teror', 'ida'),
        getParada('lanzarote', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lanzarote', 'vuelta'),
        getParada('teror', 'vuelta')
      ]
    },
    
    // Línea 222 Global: Arucas - Lanzarote (por Firgas y Valleseco)
    { 
      line: '222', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Lanzarote', 
      stopsIda: ['Firgas', 'Valleseco', 'Lanzarote'],
      stopsVuelta: ['Lanzarote', 'Valleseco', 'Firgas', 'Arucas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('arucas', 'ida'),
        getParada('firgas', 'ida'),
        getParada('valleseco', 'ida'),
        getParada('lanzarote', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lanzarote', 'vuelta'),
        getParada('valleseco', 'vuelta'),
        getParada('firgas', 'vuelta'),
        getParada('arucas', 'vuelta')
      ]
    },
    
    // Línea 223 Global: Las Palmas - Las Mesas (circular)
    { 
      line: '223', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Las Mesas', 
      stopsIda: ['Las Mesas'],
      stopsVuelta: ['Las Mesas'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('lasMesas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lasMesas', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 224 Global: Las Palmas - Lomo los Frailes
    { 
      line: '224', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Lomo los Frailes', 
      stopsIda: ['Tamaraceite', 'Lomo los Frailes'],
      stopsVuelta: ['Lomo los Frailes', 'Tamaraceite'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('tamaraceite', 'ida'),
        getParada('lomoLosFrailes', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lomoLosFrailes', 'vuelta'),
        getParada('tamaraceite', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 229 Global: Las Palmas - San José del Álamo - Teror
    { 
      line: '229', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Teror', 
      stopsIda: ['Tamaraceite', 'San José del Álamo', 'Teror'],
      stopsVuelta: ['Teror', 'San José del Álamo', 'Tamaraceite'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('tamaraceite', 'ida'),
        getParada('sanJoseAlamo', 'ida'),
        getParada('teror', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('teror', 'vuelta'),
        getParada('sanJoseAlamo', 'vuelta'),
        getParada('tamaraceite', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 232 Global: Las Palmas - Lanzarote
    { 
      line: '232', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Lanzarote', 
      stopsIda: ['Lanzarote'],
      stopsVuelta: ['Lanzarote'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('lanzarote', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('lanzarote', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 117 Global: Las Palmas - Los Dragos - Moya - Cabo Verde (inverso de 116)
    { 
      line: '117', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Cabo Verde', 
      stopsIda: ['Los Dragos', 'Moya', 'Cabo Verde'],
      stopsVuelta: ['Cabo Verde', 'Moya', 'Los Dragos'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('losDragos', 'ida'),
        getParada('moya', 'ida'),
        getParada('caboVerde', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('caboVerde', 'vuelta'),
        getParada('moya', 'vuelta'),
        getParada('losDragos', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 336 Global: Las Palmas - Universidad Fernando Pessoa
    { 
      line: '336', type: 'interurban', company: 'global', origin: 'Santa Catalina', destination: 'Univ. Fdo. Pessoa', 
      stopsIda: ['Campus Universitario', 'Univ. Fdo. Pessoa'],
      stopsVuelta: ['Univ. Fdo. Pessoa', 'Campus Universitario'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('santaCatalina', 'ida'),
        getParada('campusULPGC', 'ida'),
        getParada('univFdoPessoa', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('univFdoPessoa', 'vuelta'),
        getParada('campusULPGC', 'vuelta'),
        getParada('santaCatalina', 'vuelta')
      ]
    },
    
    // Línea 53 Global: Valle de Jinámar - C.A.E. El Calero
    { 
      line: '53', type: 'interurban', company: 'global', origin: 'Valle de Jinámar', destination: 'C.A.E. El Calero', 
      stopsIda: ['Jinámar', 'CAE Calero'],
      stopsVuelta: ['CAE Calero', 'Jinámar'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('valleJinamar', 'ida'),
        getParada('jinamar', 'ida'),
        getParada('caeCalero', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('caeCalero', 'vuelta'),
        getParada('jinamar', 'vuelta'),
        getParada('valleJinamar', 'vuelta')
      ]
    },
    
    // Línea 54 Global: Caserones - La Medianía
    { 
      line: '54', type: 'interurban', company: 'global', origin: 'Caserones', destination: 'La Medianía', 
      stopsIda: ['Agüimes', 'La Medianía'],
      stopsVuelta: ['La Medianía', 'Agüimes'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('caserones', 'ida'),
        getParada('aguimes', 'ida'),
        getParada('laMediania', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('laMediania', 'vuelta'),
        getParada('aguimes', 'vuelta'),
        getParada('caserones', 'vuelta')
      ]
    },
    
    // Línea 27 Global: Montaña Las Tierras - Agüimes
    { 
      line: '27', type: 'interurban', company: 'global', origin: 'Montaña Las Tierras', destination: 'Agüimes', 
      stopsIda: ['Agüimes'],
      stopsVuelta: ['Agüimes'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('montanaTierras', 'ida'),
        getParada('aguimes', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('aguimes', 'vuelta'),
        getParada('montanaTierras', 'vuelta')
      ]
    },
    
    // Línea 34 Global: San Bartolomé de Tirajana - Agüimes - Doctoral
    { 
      line: '34', type: 'interurban', company: 'global', origin: 'San Bartolomé', destination: 'Doctoral', 
      stopsIda: ['Agüimes', 'Doctoral'],
      stopsVuelta: ['Doctoral', 'Agüimes'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('sanBartolomeTirajana', 'ida'),
        getParada('aguimes', 'ida'),
        getParada('doctoral', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('doctoral', 'vuelta'),
        getParada('aguimes', 'vuelta'),
        getParada('sanBartolomeTirajana', 'vuelta')
      ]
    },
    
    // Línea 86 Global: Puerto de Mogán - Playa de Tasarte
    { 
      line: '86', type: 'interurban', company: 'global', origin: 'Puerto de Mogán', destination: 'Playa de Tasarte', 
      stopsIda: ['Mogán', 'Playa Tasarte'],
      stopsVuelta: ['Playa Tasarte', 'Mogán'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('puertoMogan', 'ida'),
        getParada('moganPueblo', 'ida'),
        getParada('playaTasarte', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('playaTasarte', 'vuelta'),
        getParada('moganPueblo', 'vuelta'),
        getParada('puertoMogan', 'vuelta')
      ]
    },
    
    // Línea 38 Global: Puerto de Mogán - Aldea de San Nicolás
    { 
      line: '38', type: 'interurban', company: 'global', origin: 'Puerto de Mogán', destination: 'Aldea de San Nicolás', 
      stopsIda: ['Mogán', 'Aldea San Nicolás'],
      stopsVuelta: ['Aldea San Nicolás', 'Mogán'],
      color: '#0066CC',
      routeCoordsIda: [
        getParada('puertoMogan', 'ida'),
        getParada('moganPueblo', 'ida'),
        getParada('aldeaSanNicolas', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('aldeaSanNicolas', 'vuelta'),
        getParada('moganPueblo', 'vuelta'),
        getParada('puertoMogan', 'vuelta')
      ]
    },
    
    // ========== LÍNEAS NOCTURNAS ==========
    
    // L1: Puerto - Hoya de La Plata (nocturna por León y Castillo)
    { 
      line: 'L1', type: 'night', company: 'municipales', origin: 'Puerto', destination: 'Hoya de La Plata', 
      stopsIda: ['Santa Catalina', 'Galicia', 'Mesa y López', 'Torre Las Palmas', 'Oficinas Municipales', 'León y Castillo 50', 'Paseo San José', 'Ciudad Justicia', 'Blas Cabrera'],
      stopsVuelta: ['Blas Cabrera', 'Ciudad Justicia', 'San José', 'León y Castillo 22', 'Oficinas Municipales', 'Mesa y López', 'Galicia', 'Parque Santa Catalina'],
      color: '#9933FF',
      routeCoordsIda: [
        getParada('puerto', 'ida'),
        getParada('santaCatalina', 'ida'),
        getParada('galicia', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('torreLasPalmas', 'ida'),
        getParada('oficinasMunicipales', 'ida'),
        getParada('leonCastillo50', 'ida'),
        getParada('paseoSanJose', 'ida'),
        getParada('ciudadJusticia', 'ida'),
        getParada('blasCabrera', 'ida'),
        getParada('hoyaDeLaPlata', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('hoyaDeLaPlata', 'vuelta'),
        getParada('blasCabrera', 'vuelta'),
        getParada('ciudadJusticia', 'vuelta'),
        getParada('sanJose', 'vuelta'),
        getParada('leonCastillo22', 'vuelta'),
        getParada('oficinasMunicipales', 'vuelta'),
        getParada('mesaYLopez', 'vuelta'),
        getParada('galicia', 'vuelta'),
        getParada('parqueSantaCatalina', 'vuelta'),
        getParada('puerto', 'vuelta')
      ]
    },
    
    // L2: Teatro - Santa Catalina (nocturna por Escaleritas)
    { 
      line: 'L2', type: 'night', company: 'municipales', origin: 'Teatro', destination: 'Santa Catalina', 
      stopsIda: ['San Telmo', 'Don Zoilo', 'Altavista', 'Schamann', 'Escaleritas', 'La Feria', 'Hospital Dr. Negrín', 'La Minilla', 'Mesa y López', 'Galicia'],
      stopsVuelta: ['Galicia', 'Mesa y López', 'La Minilla', 'Hospital Dr. Negrín', 'Escaleritas Mercado', 'Schamann Plaza', 'Altavista', 'Don Zoilo', 'Triana', 'San Telmo'],
      color: '#9933FF',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('donZoilo', 'ida'),
        getParada('altavista', 'ida'),
        getParada('schamann', 'ida'),
        getParada('escaleritas', 'ida'),
        getParada('laFeria', 'ida'),
        getParada('hospitalNegrin', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('galicia', 'ida'),
        getParada('santaCatalina', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('santaCatalina', 'vuelta'),
        getParada('galicia', 'vuelta'),
        getParada('mesaYLopez', 'vuelta'),
        getParada('laMinilla', 'vuelta'),
        getParada('hospitalNegrin', 'vuelta'),
        getParada('escaleritasMercado', 'vuelta'),
        getParada('schamannPlaza', 'vuelta'),
        getParada('altavista', 'vuelta'),
        getParada('donZoilo', 'vuelta'),
        getParada('triana', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // L3: Teatro - Tamaraceite (nocturna)
    { 
      line: 'L3', type: 'night', company: 'municipales', origin: 'Teatro', destination: 'Tamaraceite', 
      stopsIda: ['San Telmo', 'Don Zoilo', 'Altavista', 'La Feria', 'El Pilar', 'Siete Palmas', 'Juan Carlos I', 'Tenoya'],
      stopsVuelta: ['Tenoya', 'Juan Carlos I', 'Siete Palmas CC', 'El Pilar', 'La Feria Nº5', 'Altavista', 'Don Zoilo', 'Triana', 'San Telmo'],
      color: '#9933FF',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('sanTelmo', 'ida'),
        getParada('donZoilo', 'ida'),
        getParada('altavista', 'ida'),
        getParada('laFeria', 'ida'),
        getParada('elPilar', 'ida'),
        getParada('sietePalmas', 'ida'),
        getParada('juanCarlosI', 'ida'),
        getParada('tenoya', 'ida'),
        getParada('tamaraceite', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tamaraceite', 'vuelta'),
        getParada('tenoya', 'vuelta'),
        getParada('juanCarlosI', 'vuelta'),
        getParada('sietePalmasCC', 'vuelta'),
        getParada('elPilar', 'vuelta'),
        getParada('feriaNumero5', 'vuelta'),
        getParada('altavista', 'vuelta'),
        getParada('donZoilo', 'vuelta'),
        getParada('triana', 'vuelta'),
        getParada('sanTelmo', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // 64: Medianoche Teatro - Cono Sur
    { 
      line: '64', type: 'night', company: 'municipales', origin: 'Teatro', destination: 'Cono Sur', 
      stopsIda: ['Vegueta', 'Paseo San José', 'Ciudad Deportiva Gran Canaria', 'El Lasso', 'Casablanca', 'Pedro Hidalgo', 'Hoya de La Plata', 'Salto del Negro', 'La Montañeta'],
      stopsVuelta: ['La Montañeta', 'Salto del Negro', 'Hoya de La Plata', 'San Cristóbal', 'Teatro'],
      color: '#9933FF',
      routeCoordsIda: [
        getParada('teatro', 'ida'),
        getParada('vegueta', 'ida'),
        getParada('ciudadDeportivaGC', 'ida'),
        getParada('elLasso', 'ida'),
        getParada('casablanca', 'ida'),
        getParada('pedroHidalgo', 'ida'),
        getParada('hoyaDeLaPlata', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('hoyaDeLaPlata', 'vuelta'),
        getParada('teatro', 'vuelta')
      ]
    },
    
    // 65: Medianoche Puerto - Tamaraceite (por Mesa y López)
    { 
      line: '65', type: 'night', company: 'municipales', origin: 'Puerto', destination: 'Tamaraceite', 
      stopsIda: ['Santa Catalina', 'Galicia', 'Mesa y López', 'La Minilla', 'Guanarteme', 'Juan Carlos I', 'Siete Palmas', 'Tenoya'],
      stopsVuelta: ['Tenoya', 'Siete Palmas CC', 'Juan Carlos I', 'Guanarteme Iglesia', 'La Minilla', 'Mesa y López', 'Galicia', 'Parque Santa Catalina'],
      color: '#9933FF',
      routeCoordsIda: [
        getParada('puerto', 'ida'),
        getParada('santaCatalina', 'ida'),
        getParada('galicia', 'ida'),
        getParada('mesaYLopez', 'ida'),
        getParada('laMinilla', 'ida'),
        getParada('guanarteme', 'ida'),
        getParada('juanCarlosI', 'ida'),
        getParada('sietePalmas', 'ida'),
        getParada('tenoya', 'ida'),
        getParada('tamaraceite', 'ida')
      ],
      routeCoordsVuelta: [
        getParada('tamaraceite', 'vuelta'),
        getParada('tenoya', 'vuelta'),
        getParada('sietePalmasCC', 'vuelta'),
        getParada('juanCarlosI', 'vuelta'),
        getParada('guanartemeIglesia', 'vuelta'),
        getParada('laMinilla', 'vuelta'),
        getParada('mesaYLopez', 'vuelta'),
        getParada('galicia', 'vuelta'),
        getParada('parqueSantaCatalina', 'vuelta'),
        getParada('puerto', 'vuelta')
      ]
    }
  ]; 
  return routes;
};

export default getRoutes;
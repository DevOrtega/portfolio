<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Infrastructure\Persistence\Eloquent\Models\ProjectModel;
use App\Models\Skill;
use App\Models\PersonalInfo;
use App\Models\Experience;
use App\Models\Education;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Run Admin User Seeder
        $this->call(AdminUserSeeder::class);

        // Run Bus Data Seeder
        $this->call(BusDataSeeder::class);

        // Personal Info - Solo crear si no existe
        PersonalInfo::firstOrCreate(
            ['email' => 'carloso2103@gmail.com'],
            [
                'name' => 'Carlos Miguel Ortega Arencibia',
                'headline' => 'Desarrollador Fullstack',
                'headline_en' => 'Fullstack Developer',
                'bio' => "Soy un profesional con amplia experiencia en el desarrollo full-stack, especializado en Laravel, Vue.js y MySQL, con una sólida trayectoria en la creación, migración y mantenimiento de aplicaciones web robustas y escalables.\n\nHe liderado procesos de migración tecnológica, actualizando aplicaciones de Laravel 7 a Laravel 10 y de Vue.js 2 a Vue.js 3, además de implementar contenedores Docker y servicios web en Node.js y Java (Gradle).\n\nActualmente estoy realizando una especialización en Big Data e Inteligencia Artificial, profundizando en análisis de datos, machine learning, procesamiento de grandes volúmenes de información y desarrollo de soluciones basadas en IA. Además, me encuentro explorando el mundo del Internet de las Cosas (IoT), integrando sensores y dispositivos conectados con aplicaciones web.\n\nMe apasiona el desarrollo de soluciones tecnológicas innovadoras y el aprendizaje continuo de nuevas tecnologías.",
                'bio_en' => "I am a professional with extensive experience in full-stack development, specialized in Laravel, Vue.js, and MySQL, with a solid track record in creating, migrating, and maintaining robust and scalable web applications.\n\nI have led technology migration processes, upgrading applications from Laravel 7 to Laravel 10 and Vue.js 2 to Vue.js 3, in addition to implementing Docker containers and web services in Node.js and Java (Gradle).\n\nI am currently pursuing a specialization in Big Data and Artificial Intelligence, deepening my knowledge in data analysis, machine learning, processing large volumes of information, and developing AI-based solutions. Additionally, I am exploring the world of Internet of Things (IoT), integrating sensors and connected devices with web applications.\n\nI am passionate about developing innovative technological solutions and continuous learning of new technologies.",
                'linkedin_url' => 'https://www.linkedin.com/in/carlosmortega/',
                'github_url' => 'https://github.com/DevOrtega/',
            ]
        );

        // Skills - Solo crear si no existen
        $skills = [
            // Backend
            ['name' => 'Laravel', 'category' => 'Backend', 'proficiency' => 95],
            ['name' => 'PHP', 'category' => 'Backend', 'proficiency' => 90],
            ['name' => 'Node.js', 'category' => 'Backend', 'proficiency' => 80],
            ['name' => 'Express.js', 'category' => 'Backend', 'proficiency' => 75],
            ['name' => 'Java', 'category' => 'Backend', 'proficiency' => 70],
            ['name' => 'Python', 'category' => 'Backend', 'proficiency' => 75],
            ['name' => 'Gradle', 'category' => 'Backend', 'proficiency' => 60],

            // Frontend
            ['name' => 'Vue.js', 'category' => 'Frontend', 'proficiency' => 90],
            ['name' => 'JavaScript', 'category' => 'Frontend', 'proficiency' => 85],
            ['name' => 'TypeScript', 'category' => 'Frontend', 'proficiency' => 80],
            ['name' => 'React.js', 'category' => 'Frontend', 'proficiency' => 65],
            ['name' => 'HTML', 'category' => 'Frontend', 'proficiency' => 95],
            ['name' => 'CSS', 'category' => 'Frontend', 'proficiency' => 85],
            ['name' => 'Tailwind CSS', 'category' => 'Frontend', 'proficiency' => 85],
            ['name' => 'Angular.js', 'category' => 'Frontend', 'proficiency' => 60],

            // SQL Databases
            ['name' => 'MySQL', 'category' => 'SQL', 'proficiency' => 90],
            ['name' => 'PostgreSQL', 'category' => 'SQL', 'proficiency' => 80],
            ['name' => 'Oracle Database', 'category' => 'SQL', 'proficiency' => 75],
            ['name' => 'MariaDB', 'category' => 'SQL', 'proficiency' => 85],
            ['name' => 'BigQuery', 'category' => 'SQL', 'proficiency' => 65],

            // NoSQL Databases
            ['name' => 'MongoDB', 'category' => 'NoSQL', 'proficiency' => 70],
            ['name' => 'Redis', 'category' => 'NoSQL', 'proficiency' => 75],
            ['name' => 'Neo4j', 'category' => 'NoSQL', 'proficiency' => 60],
            ['name' => 'Hadoop', 'category' => 'NoSQL', 'proficiency' => 65],

            // Cloud & DevOps
            ['name' => 'Docker', 'category' => 'DevOps', 'proficiency' => 80],
            ['name' => 'Git', 'category' => 'DevOps', 'proficiency' => 85],
            ['name' => 'GitHub Actions', 'category' => 'DevOps', 'proficiency' => 75],
            ['name' => 'Linux', 'category' => 'DevOps', 'proficiency' => 75],
            ['name' => 'Nginx', 'category' => 'DevOps', 'proficiency' => 70],
            ['name' => 'AWS', 'category' => 'Cloud', 'proficiency' => 65],
            ['name' => 'Google Cloud', 'category' => 'Cloud', 'proficiency' => 70],
            ['name' => 'Firebase', 'category' => 'Cloud', 'proficiency' => 70],
            ['name' => 'Proxmox', 'category' => 'DevOps', 'proficiency' => 70, 'is_personal' => true],
            ['name' => 'Cloudera', 'category' => 'DevOps', 'proficiency' => 60],

            // AI Tools (LLMs & Assistants)
            ['name' => 'GitHub Copilot', 'category' => 'AI Tools', 'proficiency' => 90],
            ['name' => 'ChatGPT', 'category' => 'AI Tools', 'proficiency' => 85],
            ['name' => 'Claude AI', 'category' => 'AI Tools', 'proficiency' => 85],
            ['name' => 'Gemini', 'category' => 'AI Tools', 'proficiency' => 80],
            ['name' => 'Perplexity', 'category' => 'AI Tools', 'proficiency' => 80],
            ['name' => 'Antigravity', 'category' => 'AI Tools', 'proficiency' => 75],

            // Data Science & ML
            ['name' => 'Pandas', 'category' => 'Data Science', 'proficiency' => 70],
            ['name' => 'NumPy', 'category' => 'Data Science', 'proficiency' => 70],
            ['name' => 'TensorFlow', 'category' => 'Data Science', 'proficiency' => 65],
            ['name' => 'PyTorch', 'category' => 'Data Science', 'proficiency' => 60],
            ['name' => 'Spark', 'category' => 'Data Science', 'proficiency' => 65],

            // Tools & Methodology
            ['name' => 'VS Code', 'category' => 'Tools', 'proficiency' => 95],
            ['name' => 'Postman', 'category' => 'Tools', 'proficiency' => 85],
            ['name' => 'OpenAPI', 'category' => 'Tools', 'proficiency' => 80],
            ['name' => 'WSL', 'category' => 'Tools', 'proficiency' => 80],
            ['name' => 'Bash', 'category' => 'Tools', 'proficiency' => 80],
            ['name' => 'DBeaver', 'category' => 'Tools', 'proficiency' => 80],
            ['name' => 'Composer', 'category' => 'Tools', 'proficiency' => 90],
            ['name' => 'npm', 'category' => 'Tools', 'proficiency' => 85],
            ['name' => 'Vite', 'category' => 'Tools', 'proficiency' => 80],
            ['name' => 'Pest/PHPUnit', 'category' => 'Tools', 'proficiency' => 80],
            ['name' => 'Figma', 'category' => 'Tools', 'proficiency' => 70],
            ['name' => 'Scrum', 'category' => 'Methodology', 'proficiency' => 85],
            ['name' => 'DevOps', 'category' => 'Methodology', 'proficiency' => 75],
            ['name' => 'TDD', 'category' => 'Methodology', 'proficiency' => 80, 'is_personal' => true],
            ['name' => 'DDD', 'category' => 'Methodology', 'proficiency' => 75],
            ['name' => 'MVC', 'category' => 'Methodology', 'proficiency' => 90],
            ['name' => 'Principios SOLID', 'category' => 'Methodology', 'proficiency' => 80],
            ['name' => 'Arquitectura Hexagonal', 'category' => 'Methodology', 'proficiency' => 75, 'is_personal' => true],

            // IoT
            ['name' => 'Home Assistant', 'category' => 'IoT', 'proficiency' => 75, 'is_personal' => true],
            ['name' => 'MQTT', 'category' => 'IoT', 'proficiency' => 70],
            ['name' => 'Zigbee/Z-Wave', 'category' => 'IoT', 'proficiency' => 65],
            ['name' => 'Zigbee2MQTT', 'category' => 'IoT', 'proficiency' => 70],
            ['name' => 'Matter', 'category' => 'IoT', 'proficiency' => 60, 'is_personal' => true],
            ['name' => 'Edge Computing', 'category' => 'IoT', 'proficiency' => 65],
            ['name' => 'LPWAN/LoRa', 'category' => 'IoT', 'proficiency' => 60],
            ['name' => 'Plataformas IoT', 'category' => 'IoT', 'proficiency' => 70],
            ['name' => 'Machine Learning (IoT)', 'category' => 'IoT', 'proficiency' => 65],
        ];

        foreach ($skills as $skill) {
            Skill::firstOrCreate(
                ['name' => $skill['name'], 'category' => $skill['category']],
                [
                    'proficiency' => $skill['proficiency'],
                    'is_personal' => $skill['is_personal'] ?? false,
                ]
            );
        }

        // Experience - Solo crear si no existen
        $experiences = [
            [
                'company' => 'DESIC S.L.',
                'role' => 'Programador',
                'role_en' => 'Programmer',
                'start_date' => 'Abr. 2023',
                'end_date' => 'Nov. 2024',
                'description' => "Migración aplicaciones Laravel 7 a Laravel 10, de Vue.js 2 a Vue.js 3 y de ElementUI a ElementPlus. Creación API REST Laravel. Documentación OpenAPI. Creación de contenedor Docker Laravel para base de datos Oracle y PostgreSQL. Uso de librería Leaflet para geolocalización. Servicios Web Node.js. Servicios Web Java (Gradle).",
                'description_en' => "Migration of Laravel 7 to Laravel 10 applications, Vue.js 2 to Vue.js 3, and ElementUI to ElementPlus. Laravel REST API creation. OpenAPI documentation. Docker container creation for Laravel with Oracle and PostgreSQL databases. Use of Leaflet library for geolocation. Node.js Web Services. Java Web Services (Gradle)."
            ],
            [
                'company' => 'Fundación Universitaria de Las Palmas',
                'role' => 'Técnico informático',
                'role_en' => 'IT Technician',
                'start_date' => 'Dic. 2021',
                'end_date' => 'Dic. 2022',
                'description' => "Migración aplicación Drupal a Laravel. Creación API REST Laravel. Creación y modificación bases de datos Oracle. Mantenimiento aplicaciones PHP.",
                'description_en' => "Drupal to Laravel application migration. Laravel REST API creation. Oracle database creation and modification. PHP application maintenance."
            ],
            [
                'company' => 'Ecommium',
                'role' => 'Programador Junior',
                'role_en' => 'Junior Programmer',
                'start_date' => 'Mar. 2021',
                'end_date' => 'May. 2021',
                'description' => "Migración aplicación PHP a Laravel. Creación de componentes con React.js. Creación API REST Laravel.",
                'description_en' => "PHP to Laravel application migration. React.js component creation. Laravel REST API creation."
            ],
            [
                'company' => 'AtlanTIC Systems',
                'role' => 'Desarrollador de Aplicaciones Web',
                'role_en' => 'Web Application Developer',
                'start_date' => 'Sept. 2019',
                'end_date' => 'Mar. 2020',
                'description' => "Desarrollo de un ERP propio con los siguientes módulos: Facturación, Gestor de proyectos, Control de equipo y CRM. Participación en diversos proyectos: sector turístico, centros de formación, sector compra/venta de oro y centros deportivos. Soporte telefónico y remoto de incidencias.",
                'description_en' => "Development of a custom ERP with the following modules: Billing, Project Manager, Team Control, and CRM. Participation in various projects: tourism sector, training centers, gold buying/selling sector, and sports centers. Phone and remote incident support."
            ],
            [
                'company' => 'Satocan',
                'role' => 'Desarrollador Web',
                'role_en' => 'Web Developer',
                'start_date' => 'Dic. 2018',
                'end_date' => 'May. 2019',
                'description' => "Diseño web (estructura y diseño). Creación de BD MySQL. API Google. Validación de datos. Estructuración de permisos. Historial de cambios y modificaciones. Firma electrónica. Chat Bot Google.",
                'description_en' => "Web design (structure and design). MySQL database creation. Google API. Data validation. Permission structuring. Change and modification history. Electronic signature. Google Chat Bot."
            ]
        ];

        foreach ($experiences as $exp) {
            Experience::firstOrCreate(
                ['company' => $exp['company'], 'role' => $exp['role']],
                [
                    'role_en' => $exp['role_en'],
                    'start_date' => $exp['start_date'],
                    'end_date' => $exp['end_date'],
                    'description' => $exp['description'],
                    'description_en' => $exp['description_en']
                ]
            );
        }

        // Associate skills with experiences
        $experienceSkills = [
            'DESIC S.L.' => [
                'Laravel', 'PHP', 'Vue.js', 'JavaScript', 'Node.js', 'Java', 'Gradle',
                'PostgreSQL', 'Oracle Database', 'Docker', 'Git', 'GitHub Actions', 'Linux', 'Nginx', 'OpenAPI', 'Postman',
                'VS Code', 'DBeaver', 'Composer', 'npm', 'WSL', 'Bash', 'Vite', 'Pest/PHPUnit',
                'Scrum', 'DevOps', 'DDD', 'Principios SOLID', 'MVC', 'HTML', 'CSS', 'Tailwind CSS', 'Redis'
            ],
            'Fundación Universitaria de Las Palmas' => [
                'Laravel', 'PHP', 'Vue.js', 'JavaScript', 'Oracle Database', 'Docker', 'Git',
                'VS Code', 'Composer', 'npm', 'Postman',
                'DDD', 'Principios SOLID', 'MVC', 'HTML', 'CSS'
            ],
            'Ecommium' => [
                'Laravel', 'PHP', 'JavaScript', 'TypeScript', 'React.js', 'MySQL', 'Git', 'Linux',
                'VS Code', 'Composer', 'Postman',
                'Principios SOLID', 'MVC', 'HTML', 'CSS'
            ],
            'AtlanTIC Systems' => [
                'PHP', 'Vue.js', 'JavaScript', 'MySQL', 'MariaDB', 'Git', 'Linux',
                'VS Code', 'Composer', 'npm', 'Postman', 'Bash',
                'Principios SOLID', 'MVC', 'HTML', 'CSS'
            ],
            'Satocan' => [
                'PHP', 'Vue.js', 'JavaScript', 'Node.js', 'MySQL', 'Git', 'Firebase', 'Google Cloud',
                'VS Code', 'npm',
                'MVC', 'HTML', 'CSS'
            ],
        ];

        foreach ($experienceSkills as $companyName => $skillNames) {
            $experience = Experience::where('company', $companyName)->first();
            if ($experience) {
                $skillIds = Skill::whereIn('name', $skillNames)->pluck('id')->toArray();
                $experience->skills()->syncWithoutDetaching($skillIds);
            }
        }

        // Education - Solo crear si no existen
        $education = [
            [
                'institution' => 'IES El Rincón',
                'degree' => 'Curso de Especialización en Big Data e Inteligencia Artificial',
                'degree_en' => 'Big Data and Artificial Intelligence Specialization Course',
                'start_date' => 'Oct. 2025', // Future date in CV? Assuming typo or planned. Keeping as is.
                'end_date' => 'Mayo 2026',
                'description' => 'Formación especializada en Big Data e IA.',
                'description_en' => 'Specialized training in Big Data and AI.'
            ],
            [
                'institution' => 'Escuela de Organización Industrial',
                'degree' => 'Programa Enfocado de Internet of Things (IoT)',
                'degree_en' => 'Internet of Things (IoT) Focused Program',
                'start_date' => 'Oct. 2023',
                'end_date' => 'Dic. 2023',
                'description' => 'Programa de 96 horas impartido por expertos de Vodafone IoT, Google y empresas del sector. Módulos: introducción al IoT y estado del arte, diseño y desarrollo de sensores IoT, redes de comunicaciones IoT (arquitecturas mesh, edge computing, protocolos, LPWA, 5G), plataformas IoT y su arquitectura, gestión del dato con Big Data e Inteligencia Artificial aplicada, y estrategias empresariales para soluciones IoT.',
                'description_en' => '96-hour program taught by experts from Vodafone IoT, Google, and industry companies. Modules: IoT introduction and state of the art, IoT sensor design and development, IoT communication networks (mesh architectures, edge computing, protocols, LPWA, 5G), IoT platforms and architecture, data management with Big Data and applied Artificial Intelligence, and business strategies for IoT solutions.'
            ],
            [
                'institution' => 'Escuela de Organización Industrial',
                'degree' => 'Programación Fullstack',
                'degree_en' => 'Fullstack Programming',
                'start_date' => 'Sept. 2020',
                'end_date' => 'Dic. 2020',
                'description' => '',
                'description_en' => ''
            ],
            [
                'institution' => 'IES EL Rincón',
                'degree' => 'Desarrollo de Aplicaciones Web',
                'degree_en' => 'Web Application Development',
                'start_date' => 'Sept. 2016',
                'end_date' => 'Jun. 2019',
                'description' => '',
                'description_en' => ''
            ],
            [
                'institution' => 'ICSE',
                'degree' => 'Desarrollo de Aplicaciones con Tecnologías Web',
                'degree_en' => 'Application Development with Web Technologies',
                'start_date' => 'Mar. 2018',
                'end_date' => 'Ago. 2018',
                'description' => '',
                'description_en' => ''
            ],
            [
                'institution' => 'IES Teror',
                'degree' => 'Bachiller Científico Tecnológico',
                'degree_en' => 'Scientific and Technological Baccalaureate',
                'start_date' => 'Sept. 2014',
                'end_date' => 'Sept. 2016',
                'description' => '',
                'description_en' => ''
            ],
        ];

        foreach ($education as $edu) {
            Education::firstOrCreate(
                ['institution' => $edu['institution'], 'degree' => $edu['degree']],
                [
                    'degree_en' => $edu['degree_en'],
                    'start_date' => $edu['start_date'],
                    'end_date' => $edu['end_date'],
                    'description' => $edu['description'],
                    'description_en' => $edu['description_en']
                ]
            );
        }

        // Associate skills with education
        $educationSkills = [
            'Curso de Especialización en Big Data e Inteligencia Artificial' => [
                'Python', 'Docker', 'Linux', 'Hadoop', 'MongoDB', 'Neo4j', 'Cloudera',
                'TensorFlow', 'PyTorch', 'Pandas', 'NumPy', 'Spark',
                'Machine Learning (IoT)', 'DDD',
                'GitHub Copilot', 'ChatGPT', 'Claude AI', 'Perplexity', 'Antigravity', 'Gemini',
                'Google Cloud', 'BigQuery'
            ],
            'Programa Enfocado de Internet of Things (IoT)' => [
                'MQTT', 'Zigbee/Z-Wave', 'Zigbee2MQTT', 'BigQuery', 'Google Cloud',
                'Edge Computing', 'LPWAN/LoRa', 'Plataformas IoT', 'Machine Learning (IoT)'
            ],
            'Programación Fullstack' => [
                'Node.js', 'Express.js', 'JavaScript', 'Angular.js', 'HTML', 'CSS',
                'MongoDB', 'Redis', 'AWS', 'Docker', 'Git',
                'VS Code', 'npm', 'Figma', 'Scrum', 'DevOps'
            ],
            'Desarrollo de Aplicaciones Web' => [
                'PHP', 'JavaScript', 'HTML', 'CSS', 'Java',
                'MySQL', 'Git', 'MVC',
                'VS Code'
            ],
            'Desarrollo de Aplicaciones con Tecnologías Web' => [
                'PHP', 'JavaScript', 'HTML', 'CSS', 'MySQL'
            ],
        ];

        foreach ($educationSkills as $degreeName => $skillNames) {
            $education = Education::where('degree', $degreeName)->first();
            if ($education) {
                $skillIds = Skill::whereIn('name', $skillNames)->pluck('id')->toArray();
                $education->skills()->syncWithoutDetaching($skillIds);
            }
        }

        // Projects - Solo crear si no existen
        $projects = [
            [
                'title' => 'Gestor de Cajeros Automáticos',
                'title_en' => 'ATM Manager',
                'description' => 'Sistema de gestión y visualización de cajeros automáticos en Gran Canaria. Incluye mapa interactivo con marcadores personalizados, información detallada y filtros por entidad bancaria.',
                'description_en' => 'ATM management and visualization system in Gran Canaria. Includes interactive map with custom markers, detailed information, and filters by banking entity.',
                'tags' => ['Vue.js', 'Leaflet', 'Demo', 'Geolocalización'],
                'url' => '/projects/demo/atm-manager',
                'image_path' => '/images/atm-preview.png'
            ],
            [
                'title' => 'Seguimiento de Guaguas en Tiempo Real',
                'title_en' => 'Real-time Bus Tracking',
                'description' => 'Aplicación de seguimiento en tiempo real del transporte público de Gran Canaria. Visualiza la ubicación de las guaguas en el mapa, información de rutas, tiempos de llegada a las próximas paradas y estado de retrasos.',
                'description_en' => 'Real-time tracking application for Gran Canaria public transport. Displays bus locations on the map, route information, arrival times to upcoming stops, and delay status.',
                'tags' => ['Vue.js', 'Leaflet', 'Real-time', 'API', 'Demo'],
                'url' => '/projects/demo/guaguas-tracker',
                'image_path' => '/images/guaguas-preview.png'
            ],
        ];

        foreach ($projects as $project) {
            ProjectModel::firstOrCreate(
                ['title' => $project['title']],
                $project
            );
        }
    }
}

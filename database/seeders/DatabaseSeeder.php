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
        // Personal Info
        PersonalInfo::create([
            'name' => 'Carlos Miguel Ortega Arencibia',
            'headline' => 'Programador/Desarrollador de Aplicaciones Web',
            'bio' => 'Me dirijo a usted con el propósito de expresar mi interés en formar parte de su equipo como Desarrollador de Aplicaciones Web. Soy un profesional con amplia experiencia en el desarrollo full-stack, especializado en Laravel, Vue.js y MySQL, y con una sólida trayectoria en la creación, migración y mantenimiento de aplicaciones web robustas y escalables. Durante mi experiencia en DESIC S.L., he liderado procesos de migración tecnológica, actualizando aplicaciones de Laravel 7 a Laravel 10 y de Vue.js 2 a Vue.js 3, además de implementar contenedores Docker y servicios web en Node.js y Java (Gradle).',
            'email' => 'carloso2103@gmail.com',
            'linkedin_url' => 'https://www.linkedin.com/in/carlosmortega/',
            'github_url' => 'https://github.com/DevOrtega',
            // 'phone' => '+34 650-396-943', // Not in schema but good to know
            // 'location' => 'Las Palmas De Gran Canaria, Spain',
        ]);

        // Skills
        $skills = [
            // Backend
            ['name' => 'Laravel', 'category' => 'Backend', 'proficiency' => 95],
            ['name' => 'PHP', 'category' => 'Backend', 'proficiency' => 90],
            ['name' => 'Node.js', 'category' => 'Backend', 'proficiency' => 80],
            ['name' => 'Java', 'category' => 'Backend', 'proficiency' => 70],
            ['name' => 'Python', 'category' => 'Backend', 'proficiency' => 75],
            ['name' => 'Gradle', 'category' => 'Backend', 'proficiency' => 60],

            // Frontend
            ['name' => 'Vue.js', 'category' => 'Frontend', 'proficiency' => 90],
            ['name' => 'JavaScript', 'category' => 'Frontend', 'proficiency' => 85],
            ['name' => 'TypeScript', 'category' => 'Frontend', 'proficiency' => 80],
            ['name' => 'HTML', 'category' => 'Frontend', 'proficiency' => 95],
            ['name' => 'CSS', 'category' => 'Frontend', 'proficiency' => 85],
            ['name' => 'Angular.js', 'category' => 'Frontend', 'proficiency' => 60],

            // Database
            ['name' => 'MySQL', 'category' => 'Database', 'proficiency' => 90],
            ['name' => 'PostgreSQL', 'category' => 'Database', 'proficiency' => 80],
            ['name' => 'Oracle Database', 'category' => 'Database', 'proficiency' => 75],
            ['name' => 'MongoDB', 'category' => 'Database', 'proficiency' => 70],
            ['name' => 'MariaDB', 'category' => 'Database', 'proficiency' => 85],
            ['name' => 'Neo4j', 'category' => 'Database', 'proficiency' => 60],
            ['name' => 'Hadoop', 'category' => 'Database', 'proficiency' => 65],

            // DevOps & Tools
            ['name' => 'Docker', 'category' => 'DevOps', 'proficiency' => 80],
            ['name' => 'Git', 'category' => 'DevOps', 'proficiency' => 85],
            ['name' => 'AWS', 'category' => 'DevOps', 'proficiency' => 60],
            ['name' => 'Proxmox', 'category' => 'DevOps', 'proficiency' => 70],
            ['name' => 'Cloudera', 'category' => 'DevOps', 'proficiency' => 60],
            ['name' => 'OpenAPI', 'category' => 'Tools', 'proficiency' => 80],
            ['name' => 'Scrum', 'category' => 'Methodology', 'proficiency' => 85],
        ];

        foreach ($skills as $skill) {
            Skill::create($skill);
        }

        // Experience
        $experiences = [
            [
                'company' => 'DESIC S.L.',
                'role' => 'Programador',
                'start_date' => 'Abr. 2023',
                'end_date' => 'Nov. 2024',
                'description' => "Migración aplicaciones Laravel 7 a Laravel 10, de Vue.js 2 a Vue.js 3 y de ElementUI a ElementPlus. Creación API REST Laravel. Documentación OpenAPI. Creación de contenedor Docker Laravel para base de datos Oracle y PostgreSQL. Uso de librería Leaflet para geolocalización. Servicios Web Node.js. Servicios Web Java (Gradle)."
            ],
            [
                'company' => 'Fundación Universitaria de Las Palmas',
                'role' => 'Técnico informático',
                'start_date' => 'Dic. 2021',
                'end_date' => 'Dic. 2022',
                'description' => "Migración aplicación Drupal a Laravel. Creación API REST Laravel. Creación y modificación bases de datos Oracle. Mantenimiento aplicaciones PHP."
            ],
            [
                'company' => 'Ecommium',
                'role' => 'Programador Junior',
                'start_date' => 'Mar. 2021',
                'end_date' => 'May. 2021',
                'description' => "Migración aplicación PHP a Laravel. Creación de componentes con React.js. Creación API REST Laravel."
            ],
            [
                'company' => 'AtlanTIC Systems',
                'role' => 'Desarrollador de Aplicaciones Web',
                'start_date' => 'Sept. 2019',
                'end_date' => 'Mar. 2020',
                'description' => "Desarrollo de un ERP propio con los siguientes módulos: Facturación, Gestor de proyectos, Control de equipo y CRM. Participación en diversos proyectos: sector turístico, centros de formación, sector compra/venta de oro y centros deportivos. Soporte telefónico y remoto de incidencias."
            ],
            [
                'company' => 'Satocan',
                'role' => 'Desarrollador Web',
                'start_date' => 'Dic. 2018',
                'end_date' => 'May. 2019',
                'description' => "Diseño web (estructura y diseño). Creación de BD MySQL. API Google. Validación de datos. Estructuración de permisos. Historial de cambios y modificaciones. Firma electrónica. Chat Bot Google."
            ]
        ];

        foreach ($experiences as $exp) {
            Experience::create($exp);
        }

        // Education
        $education = [
            [
                'institution' => 'IES El Rincón',
                'degree' => 'Curso de Especialización en Big Data e Inteligencia Artificial',
                'start_date' => 'Oct. 2025', // Future date in CV? Assuming typo or planned. Keeping as is.
                'end_date' => 'Mayo 2026',
                'description' => 'Formación especializada en Big Data e IA.'
            ],
            [
                'institution' => 'Escuela de Organización Industrial',
                'degree' => 'Programa Enfocado de Internet of Things (IoT)',
                'start_date' => 'Oct. 2023',
                'end_date' => 'Dic. 2023',
                'description' => ''
            ],
            [
                'institution' => 'Escuela de Organización Industrial',
                'degree' => 'Programación Fullstack',
                'start_date' => 'Sept. 2020',
                'end_date' => 'Dic. 2020',
                'description' => ''
            ],
            [
                'institution' => 'IES EL Rincón',
                'degree' => 'Desarrollo de Aplicaciones Web',
                'start_date' => 'Sept. 2016',
                'end_date' => 'Jun. 2019',
                'description' => ''
            ],
            [
                'institution' => 'ICSE',
                'degree' => 'Desarrollo de Aplicaciones con Tecnologías Web',
                'start_date' => 'Mar. 2018',
                'end_date' => 'Ago. 2018',
                'description' => ''
            ],
            [
                'institution' => 'IES Teror',
                'degree' => 'Bachiller Científico Tecnológico',
                'start_date' => 'Sept. 2014',
                'end_date' => 'Sept. 2016',
                'description' => ''
            ],
        ];

        foreach ($education as $edu) {
            Education::create($edu);
        }

        // Projects (Keeping existing ones + inferred)
        $projects = [
            [
                'title' => 'Gamerspot_Frontend',
                'description' => 'Frontend for Gamerspot application using TypeScript.',
                'tags' => ['TypeScript', 'Frontend', 'Vue.js'],
                'github_url' => 'https://github.com/DevOrtega/Gamerspot_Frontend'
            ],
            [
                'title' => 'Gamerspot_Backend',
                'description' => 'Backend for Gamerspot application using Node.js.',
                'tags' => ['JavaScript', 'Backend', 'Node.js'],
                'github_url' => 'https://github.com/DevOrtega/Gamerspot_Backend'
            ],
            [
                'title' => 'Fruits_Crud_Backend',
                'description' => 'CRUD Backend for Fruits application.',
                'tags' => ['PHP', 'Backend', 'Laravel'],
                'github_url' => 'https://github.com/DevOrtega/Fruits_Crud_Backend'
            ],
            [
                'title' => 'Fruits_Crud_Frontend',
                'description' => 'CRUD Frontend for Fruits application.',
                'tags' => ['TypeScript', 'Frontend'],
                'github_url' => 'https://github.com/DevOrtega/Fruits_Crud_Frontend'
            ],
            [
                'title' => 'Vue Leaflet Demo',
                'description' => 'Interactive map demonstration using Vue 3 and Leaflet. Features custom markers, popups, and geometric shapes.',
                'tags' => ['Vue.js', 'Leaflet', 'Demo'],
                'url' => '/projects/demo/map', // Internal link
                'image_path' => '/images/map-preview.png' // Placeholder or we can use a generic map icon
            ],
        ];

        foreach ($projects as $project) {
            ProjectModel::create($project);
        }
    }
}

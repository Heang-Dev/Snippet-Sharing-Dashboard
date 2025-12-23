<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            [
                'name' => 'JavaScript',
                'pygments_lexer' => 'javascript',
                'file_extensions' => ['js', 'mjs', 'cjs'],
                'icon' => 'javascript',
                'color' => '#f7df1e',
            ],
            [
                'name' => 'TypeScript',
                'pygments_lexer' => 'typescript',
                'file_extensions' => ['ts', 'tsx'],
                'icon' => 'typescript',
                'color' => '#3178c6',
            ],
            [
                'name' => 'Python',
                'pygments_lexer' => 'python',
                'file_extensions' => ['py', 'pyw', 'pyi'],
                'icon' => 'python',
                'color' => '#3776ab',
            ],
            [
                'name' => 'Java',
                'pygments_lexer' => 'java',
                'file_extensions' => ['java'],
                'icon' => 'java',
                'color' => '#007396',
            ],
            [
                'name' => 'Kotlin',
                'pygments_lexer' => 'kotlin',
                'file_extensions' => ['kt', 'kts'],
                'icon' => 'kotlin',
                'color' => '#7f52ff',
            ],
            [
                'name' => 'PHP',
                'pygments_lexer' => 'php',
                'file_extensions' => ['php', 'phtml', 'php3', 'php4', 'php5'],
                'icon' => 'php',
                'color' => '#777bb4',
            ],
            [
                'name' => 'C',
                'slug' => 'c-lang',
                'pygments_lexer' => 'c',
                'file_extensions' => ['c', 'h'],
                'icon' => 'c',
                'color' => '#a8b9cc',
            ],
            [
                'name' => 'C++',
                'slug' => 'cpp',
                'pygments_lexer' => 'cpp',
                'file_extensions' => ['cpp', 'cc', 'cxx', 'hpp', 'hh', 'hxx'],
                'icon' => 'cplusplus',
                'color' => '#00599c',
            ],
            [
                'name' => 'C#',
                'slug' => 'csharp',
                'pygments_lexer' => 'csharp',
                'file_extensions' => ['cs'],
                'icon' => 'csharp',
                'color' => '#239120',
            ],
            [
                'name' => 'Go',
                'pygments_lexer' => 'go',
                'file_extensions' => ['go'],
                'icon' => 'go',
                'color' => '#00add8',
            ],
            [
                'name' => 'Rust',
                'pygments_lexer' => 'rust',
                'file_extensions' => ['rs'],
                'icon' => 'rust',
                'color' => '#dea584',
            ],
            [
                'name' => 'Swift',
                'pygments_lexer' => 'swift',
                'file_extensions' => ['swift'],
                'icon' => 'swift',
                'color' => '#fa7343',
            ],
            [
                'name' => 'Ruby',
                'pygments_lexer' => 'ruby',
                'file_extensions' => ['rb', 'rake', 'gemspec'],
                'icon' => 'ruby',
                'color' => '#cc342d',
            ],
            [
                'name' => 'HTML',
                'pygments_lexer' => 'html',
                'file_extensions' => ['html', 'htm', 'xhtml'],
                'icon' => 'html5',
                'color' => '#e34f26',
            ],
            [
                'name' => 'CSS',
                'pygments_lexer' => 'css',
                'file_extensions' => ['css'],
                'icon' => 'css3',
                'color' => '#1572b6',
            ],
            [
                'name' => 'SCSS',
                'pygments_lexer' => 'scss',
                'file_extensions' => ['scss'],
                'icon' => 'sass',
                'color' => '#cc6699',
            ],
            [
                'name' => 'SQL',
                'pygments_lexer' => 'sql',
                'file_extensions' => ['sql'],
                'icon' => 'database',
                'color' => '#e38c00',
            ],
            [
                'name' => 'Bash',
                'pygments_lexer' => 'bash',
                'file_extensions' => ['sh', 'bash', 'zsh'],
                'icon' => 'bash',
                'color' => '#4eaa25',
            ],
            [
                'name' => 'PowerShell',
                'pygments_lexer' => 'powershell',
                'file_extensions' => ['ps1', 'psm1', 'psd1'],
                'icon' => 'powershell',
                'color' => '#5391fe',
            ],
            [
                'name' => 'JSON',
                'pygments_lexer' => 'json',
                'file_extensions' => ['json'],
                'icon' => 'json',
                'color' => '#000000',
            ],
            [
                'name' => 'YAML',
                'pygments_lexer' => 'yaml',
                'file_extensions' => ['yaml', 'yml'],
                'icon' => 'yaml',
                'color' => '#cb171e',
            ],
            [
                'name' => 'XML',
                'pygments_lexer' => 'xml',
                'file_extensions' => ['xml', 'xsl', 'xslt'],
                'icon' => 'xml',
                'color' => '#0060ac',
            ],
            [
                'name' => 'Markdown',
                'pygments_lexer' => 'markdown',
                'file_extensions' => ['md', 'markdown'],
                'icon' => 'markdown',
                'color' => '#083fa1',
            ],
            [
                'name' => 'Docker',
                'pygments_lexer' => 'docker',
                'file_extensions' => ['dockerfile'],
                'icon' => 'docker',
                'color' => '#2496ed',
            ],
            [
                'name' => 'GraphQL',
                'pygments_lexer' => 'graphql',
                'file_extensions' => ['graphql', 'gql'],
                'icon' => 'graphql',
                'color' => '#e10098',
            ],
            [
                'name' => 'Dart',
                'pygments_lexer' => 'dart',
                'file_extensions' => ['dart'],
                'icon' => 'dart',
                'color' => '#0175c2',
            ],
            [
                'name' => 'Scala',
                'pygments_lexer' => 'scala',
                'file_extensions' => ['scala', 'sc'],
                'icon' => 'scala',
                'color' => '#dc322f',
            ],
            [
                'name' => 'Lua',
                'pygments_lexer' => 'lua',
                'file_extensions' => ['lua'],
                'icon' => 'lua',
                'color' => '#000080',
            ],
            [
                'name' => 'Perl',
                'pygments_lexer' => 'perl',
                'file_extensions' => ['pl', 'pm'],
                'icon' => 'perl',
                'color' => '#39457e',
            ],
            [
                'name' => 'R',
                'pygments_lexer' => 'r',
                'file_extensions' => ['r', 'R'],
                'icon' => 'r',
                'color' => '#276dc3',
            ],
            [
                'name' => 'Plain Text',
                'pygments_lexer' => 'text',
                'file_extensions' => ['txt'],
                'icon' => 'text',
                'color' => '#666666',
            ],
        ];

        foreach ($languages as $language) {
            Language::create([
                ...$language,
                'slug' => $language['slug'] ?? Str::slug($language['name']),
                'display_name' => $language['name'],
                'is_active' => true,
            ]);
        }
    }
}

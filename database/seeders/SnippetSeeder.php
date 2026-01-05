<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Language;
use App\Models\Snippet;
use App\Models\SnippetVersion;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class SnippetSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $languages = Language::all();
        $categories = Category::whereNotNull('parent_category_id')->get();
        $tags = Tag::all();

        if ($users->isEmpty() || $languages->isEmpty()) {
            $this->command->warn('Please run UserSeeder and LanguageSeeder first.');
            return;
        }

        $snippets = [
            [
                'title' => 'Quick Sort Algorithm',
                'description' => 'An efficient implementation of the QuickSort algorithm in JavaScript',
                'code' => <<<'CODE'
function quickSort(arr) {
    if (arr.length <= 1) return arr;

    const pivot = arr[Math.floor(arr.length / 2)];
    const left = arr.filter(x => x < pivot);
    const middle = arr.filter(x => x === pivot);
    const right = arr.filter(x => x > pivot);

    return [...quickSort(left), ...middle, ...quickSort(right)];
}

// Example usage
const unsorted = [64, 34, 25, 12, 22, 11, 90];
console.log(quickSort(unsorted)); // [11, 12, 22, 25, 34, 64, 90]
CODE,
                'language' => 'javascript',
                'privacy' => 'public',
                'tags' => ['algorithm', 'tutorial'],
            ],
            [
                'title' => 'React Custom Hook - useLocalStorage',
                'description' => 'A custom React hook for managing localStorage with state synchronization',
                'code' => <<<'CODE'
import { useState, useEffect } from 'react';

function useLocalStorage<T>(key: string, initialValue: T) {
    const [storedValue, setStoredValue] = useState<T>(() => {
        try {
            const item = window.localStorage.getItem(key);
            return item ? JSON.parse(item) : initialValue;
        } catch (error) {
            console.error(error);
            return initialValue;
        }
    });

    const setValue = (value: T | ((val: T) => T)) => {
        try {
            const valueToStore = value instanceof Function ? value(storedValue) : value;
            setStoredValue(valueToStore);
            window.localStorage.setItem(key, JSON.stringify(valueToStore));
        } catch (error) {
            console.error(error);
        }
    };

    return [storedValue, setValue] as const;
}

export default useLocalStorage;
CODE,
                'language' => 'typescript',
                'privacy' => 'public',
                'tags' => ['react', 'frontend', 'utility'],
            ],
            [
                'title' => 'Python Binary Search',
                'description' => 'Binary search implementation with both iterative and recursive approaches',
                'code' => <<<'CODE'
def binary_search_iterative(arr, target):
    """Iterative binary search implementation"""
    left, right = 0, len(arr) - 1

    while left <= right:
        mid = (left + right) // 2
        if arr[mid] == target:
            return mid
        elif arr[mid] < target:
            left = mid + 1
        else:
            right = mid - 1

    return -1

def binary_search_recursive(arr, target, left=0, right=None):
    """Recursive binary search implementation"""
    if right is None:
        right = len(arr) - 1

    if left > right:
        return -1

    mid = (left + right) // 2

    if arr[mid] == target:
        return mid
    elif arr[mid] < target:
        return binary_search_recursive(arr, target, mid + 1, right)
    else:
        return binary_search_recursive(arr, target, left, mid - 1)

# Example usage
sorted_array = [1, 3, 5, 7, 9, 11, 13, 15]
print(binary_search_iterative(sorted_array, 7))  # Output: 3
print(binary_search_recursive(sorted_array, 11))  # Output: 5
CODE,
                'language' => 'python',
                'privacy' => 'public',
                'tags' => ['algorithm', 'data-structure'],
            ],
            [
                'title' => 'Laravel API Resource with Pagination',
                'description' => 'Example of Laravel API Resource with proper pagination handling',
                'code' => <<<'CODE'
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SnippetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'code' => $this->code,
            'language' => new LanguageResource($this->whenLoaded('language')),
            'author' => new UserResource($this->whenLoaded('user')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'stats' => [
                'views' => $this->view_count,
                'favorites' => $this->favorite_count,
                'comments' => $this->comment_count,
            ],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}

// In Controller:
public function index(Request $request)
{
    $snippets = Snippet::with(['language', 'user', 'tags'])
        ->public()
        ->paginate(15);

    return SnippetResource::collection($snippets);
}
CODE,
                'language' => 'php',
                'privacy' => 'public',
                'tags' => ['laravel', 'api', 'backend'],
            ],
            [
                'title' => 'CSS Grid Layout Template',
                'description' => 'A responsive CSS Grid layout for dashboard interfaces',
                'code' => <<<'CODE'
.dashboard {
    display: grid;
    grid-template-columns: 250px 1fr;
    grid-template-rows: 60px 1fr auto;
    grid-template-areas:
        "sidebar header"
        "sidebar main"
        "sidebar footer";
    min-height: 100vh;
    gap: 0;
}

.header {
    grid-area: header;
    background: #1a1a2e;
    padding: 0 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar {
    grid-area: sidebar;
    background: #16213e;
    padding: 1rem;
}

.main {
    grid-area: main;
    background: #0f0f23;
    padding: 2rem;
    overflow-y: auto;
}

.footer {
    grid-area: footer;
    background: #1a1a2e;
    padding: 1rem 2rem;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard {
        grid-template-columns: 1fr;
        grid-template-areas:
            "header"
            "main"
            "footer";
    }

    .sidebar {
        display: none;
    }
}
CODE,
                'language' => 'css',
                'privacy' => 'public',
                'tags' => ['css', 'frontend', 'utility'],
            ],
            [
                'title' => 'Go HTTP Server with Middleware',
                'description' => 'A simple Go HTTP server with logging and authentication middleware',
                'code' => <<<'CODE'
package main

import (
    "fmt"
    "log"
    "net/http"
    "time"
)

// Middleware type
type Middleware func(http.Handler) http.Handler

// LoggingMiddleware logs all incoming requests
func LoggingMiddleware(next http.Handler) http.Handler {
    return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        start := time.Now()
        next.ServeHTTP(w, r)
        log.Printf("%s %s %v", r.Method, r.URL.Path, time.Since(start))
    })
}

// AuthMiddleware checks for valid authorization
func AuthMiddleware(next http.Handler) http.Handler {
    return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
        token := r.Header.Get("Authorization")
        if token == "" {
            http.Error(w, "Unauthorized", http.StatusUnauthorized)
            return
        }
        next.ServeHTTP(w, r)
    })
}

// Chain applies middlewares to a handler
func Chain(h http.Handler, middlewares ...Middleware) http.Handler {
    for _, m := range middlewares {
        h = m(h)
    }
    return h
}

func main() {
    mux := http.NewServeMux()

    mux.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
        fmt.Fprintln(w, "Hello, World!")
    })

    handler := Chain(mux, LoggingMiddleware, AuthMiddleware)

    log.Println("Server starting on :8080")
    log.Fatal(http.ListenAndServe(":8080", handler))
}
CODE,
                'language' => 'go',
                'privacy' => 'public',
                'tags' => ['backend', 'api', 'authentication'],
            ],
            [
                'title' => 'SQL Query Optimization Examples',
                'description' => 'Common SQL query optimization techniques with examples',
                'code' => <<<'CODE'
-- 1. Use EXPLAIN to analyze query performance
EXPLAIN ANALYZE
SELECT u.*, COUNT(s.id) as snippet_count
FROM users u
LEFT JOIN snippets s ON u.id = s.user_id
GROUP BY u.id;

-- 2. Create indexes for frequently queried columns
CREATE INDEX idx_snippets_user_id ON snippets(user_id);
CREATE INDEX idx_snippets_language_id ON snippets(language_id);
CREATE INDEX idx_snippets_created_at ON snippets(created_at DESC);

-- 3. Use EXISTS instead of IN for subqueries
-- Bad:
SELECT * FROM users WHERE id IN (SELECT user_id FROM snippets WHERE privacy = 'public');

-- Good:
SELECT * FROM users u
WHERE EXISTS (SELECT 1 FROM snippets s WHERE s.user_id = u.id AND s.privacy = 'public');

-- 4. Avoid SELECT * in production
-- Bad:
SELECT * FROM snippets;

-- Good:
SELECT id, title, description, created_at FROM snippets;

-- 5. Use LIMIT for pagination
SELECT id, title, description
FROM snippets
WHERE privacy = 'public'
ORDER BY created_at DESC
LIMIT 20 OFFSET 0;

-- 6. Composite index for multiple conditions
CREATE INDEX idx_snippets_composite ON snippets(privacy, language_id, created_at DESC);
CODE,
                'language' => 'sql',
                'privacy' => 'public',
                'tags' => ['database', 'performance', 'backend'],
            ],
            [
                'title' => 'Docker Compose for Laravel App',
                'description' => 'Complete Docker Compose setup for Laravel with MySQL and Redis',
                'code' => <<<'CODE'
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: laravel_app
    restart: unless-stopped
    working_dir: /var/www
    volumes:
      - ./:/var/www
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
    networks:
      - laravel

  nginx:
    image: nginx:alpine
    container_name: laravel_nginx
    restart: unless-stopped
    ports:
      - "8000:80"
    volumes:
      - ./:/var/www
      - ./docker/nginx/conf.d:/etc/nginx/conf.d
    depends_on:
      - app
    networks:
      - laravel

  mysql:
    image: mysql:8.0
    container_name: laravel_mysql
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_USER: ${DB_USERNAME}
    volumes:
      - mysql_data:/var/lib/mysql
    ports:
      - "3306:3306"
    networks:
      - laravel

  redis:
    image: redis:alpine
    container_name: laravel_redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    networks:
      - laravel

networks:
  laravel:
    driver: bridge

volumes:
  mysql_data:
    driver: local
CODE,
                'language' => 'yaml',
                'privacy' => 'public',
                'tags' => ['docker', 'laravel', 'backend'],
            ],
            [
                'title' => 'Rust Error Handling Pattern',
                'description' => 'Idiomatic error handling in Rust using Result and custom errors',
                'code' => <<<'CODE'
use std::fmt;
use std::io;

// Custom error type
#[derive(Debug)]
enum AppError {
    IoError(io::Error),
    ParseError(String),
    NotFound(String),
    Unauthorized,
}

impl fmt::Display for AppError {
    fn fmt(&self, f: &mut fmt::Formatter<'_>) -> fmt::Result {
        match self {
            AppError::IoError(e) => write!(f, "IO error: {}", e),
            AppError::ParseError(msg) => write!(f, "Parse error: {}", msg),
            AppError::NotFound(item) => write!(f, "Not found: {}", item),
            AppError::Unauthorized => write!(f, "Unauthorized access"),
        }
    }
}

impl From<io::Error> for AppError {
    fn from(error: io::Error) -> Self {
        AppError::IoError(error)
    }
}

// Function using Result
fn find_user(id: u32) -> Result<String, AppError> {
    if id == 0 {
        return Err(AppError::NotFound("User".to_string()));
    }
    Ok(format!("User_{}", id))
}

fn main() {
    match find_user(1) {
        Ok(user) => println!("Found: {}", user),
        Err(e) => eprintln!("Error: {}", e),
    }

    // Using ? operator
    let result: Result<(), AppError> = (|| {
        let user = find_user(0)?;
        println!("{}", user);
        Ok(())
    })();

    if let Err(e) = result {
        eprintln!("Operation failed: {}", e);
    }
}
CODE,
                'language' => 'rust',
                'privacy' => 'public',
                'tags' => ['backend', 'utility'],
            ],
            [
                'title' => 'JWT Authentication Middleware',
                'description' => 'Express.js middleware for JWT token validation',
                'code' => <<<'CODE'
const jwt = require('jsonwebtoken');

const JWT_SECRET = process.env.JWT_SECRET || 'your-secret-key';

const authMiddleware = (req, res, next) => {
    const authHeader = req.headers.authorization;

    if (!authHeader) {
        return res.status(401).json({
            success: false,
            message: 'No token provided'
        });
    }

    const parts = authHeader.split(' ');

    if (parts.length !== 2 || parts[0] !== 'Bearer') {
        return res.status(401).json({
            success: false,
            message: 'Token format invalid'
        });
    }

    const token = parts[1];

    try {
        const decoded = jwt.verify(token, JWT_SECRET);
        req.user = decoded;
        next();
    } catch (error) {
        if (error.name === 'TokenExpiredError') {
            return res.status(401).json({
                success: false,
                message: 'Token expired'
            });
        }
        return res.status(401).json({
            success: false,
            message: 'Invalid token'
        });
    }
};

// Generate token
const generateToken = (payload, expiresIn = '24h') => {
    return jwt.sign(payload, JWT_SECRET, { expiresIn });
};

module.exports = { authMiddleware, generateToken };
CODE,
                'language' => 'javascript',
                'privacy' => 'public',
                'tags' => ['authentication', 'nodejs', 'api', 'security'],
            ],
            [
                'title' => 'Private Team Configuration',
                'description' => 'Internal team configuration for project setup',
                'code' => <<<'CODE'
# Team Configuration
team:
  name: "Development Team"
  lead: "john@example.com"

environments:
  development:
    url: "http://localhost:3000"
    debug: true
  staging:
    url: "https://staging.example.com"
    debug: false
  production:
    url: "https://app.example.com"
    debug: false

deploy:
  branch: main
  auto_deploy: true
CODE,
                'language' => 'yaml',
                'privacy' => 'private',
                'tags' => [],
            ],
            [
                'title' => 'Team Shared Utils',
                'description' => 'Utility functions shared within the team',
                'code' => <<<'CODE'
<?php

namespace App\Utils;

class TeamUtils
{
    public static function formatDate($date, $format = 'Y-m-d H:i:s')
    {
        return $date->format($format);
    }

    public static function generateSlug($text)
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $text));
    }

    public static function truncate($text, $length = 100)
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }
}
CODE,
                'language' => 'php',
                'privacy' => 'team',
                'tags' => ['utility', 'laravel'],
            ],
        ];

        foreach ($snippets as $index => $snippetData) {
            $user = $users->random();
            $language = $languages->where('slug', $snippetData['language'])->first()
                ?? $languages->where('name', ucfirst($snippetData['language']))->first()
                ?? $languages->first();

            $snippet = Snippet::create([
                'user_id' => $user->id,
                'title' => $snippetData['title'],
                'description' => $snippetData['description'],
                'code' => $snippetData['code'],
                'language' => $language->id,
                'category_id' => $categories->isNotEmpty() ? $categories->random()->id : null,
                'privacy' => $snippetData['privacy'],
                'view_count' => rand(10, 1000),
                'favorite_count' => rand(0, 100),
                'comment_count' => rand(0, 20),
            ]);

            // Create initial version
            SnippetVersion::create([
                'snippet_id' => $snippet->id,
                'version_number' => 1,
                'title' => $snippet->title,
                'description' => $snippet->description,
                'code' => $snippet->code,
                'language' => $language->slug,
                'change_summary' => 'Initial version',
                'change_type' => 'create',
                'lines_added' => substr_count($snippet->code, "\n") + 1,
                'lines_removed' => 0,
                'created_by' => $user->id,
            ]);

            // Attach tags
            if (!empty($snippetData['tags']) && $tags->isNotEmpty()) {
                $tagIds = $tags->whereIn('name', $snippetData['tags'])->pluck('id')->toArray();
                if (!empty($tagIds)) {
                    $snippet->tags()->attach($tagIds);
                }
            }
        }
    }
}

import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Badge } from '@/Components/ui/badge';
import { Input } from '@/Components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { Plus, Search, Eye, Star, Clock, Code2, Filter, X } from 'lucide-react';
import { useState } from 'react';
import { formatDistanceToNow } from '@/lib/utils';

export default function SnippetsIndex({ snippets, languages, categories, filters }) {
    const [search, setSearch] = useState(filters.search || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/snippets', { ...filters, search }, { preserveState: true });
    };

    const handleFilterChange = (key, value) => {
        router.get('/snippets', { ...filters, [key]: value === 'all' ? '' : value }, { preserveState: true });
    };

    const clearFilters = () => {
        setSearch('');
        router.get('/snippets', {}, { preserveState: true });
    };

    const hasFilters = filters.search || filters.language || filters.visibility || filters.category;

    return (
        <AppLayout title="My Snippets">
            <Head title="My Snippets" />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-2xl font-bold tracking-tight">My Snippets</h2>
                        <p className="text-muted-foreground">
                            Manage and organize your code snippets
                        </p>
                    </div>
                    <Link href="/snippets/create">
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            New Snippet
                        </Button>
                    </Link>
                </div>

                {/* Filters */}
                <Card>
                    <CardContent className="pt-6">
                        <div className="flex flex-col gap-4 md:flex-row md:items-center">
                            <form onSubmit={handleSearch} className="flex flex-1 gap-2">
                                <div className="relative flex-1">
                                    <Search className="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                    <Input
                                        placeholder="Search snippets..."
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        className="pl-9"
                                    />
                                </div>
                                <Button type="submit" variant="secondary">
                                    Search
                                </Button>
                            </form>

                            <div className="flex gap-2">
                                <Select
                                    value={filters.language || 'all'}
                                    onValueChange={(value) => handleFilterChange('language', value)}
                                >
                                    <SelectTrigger className="w-[150px]">
                                        <SelectValue placeholder="Language" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All Languages</SelectItem>
                                        {languages.map((lang) => (
                                            <SelectItem key={lang.id} value={lang.id}>
                                                {lang.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>

                                <Select
                                    value={filters.visibility || 'all'}
                                    onValueChange={(value) => handleFilterChange('visibility', value)}
                                >
                                    <SelectTrigger className="w-[130px]">
                                        <SelectValue placeholder="Visibility" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="all">All</SelectItem>
                                        <SelectItem value="public">Public</SelectItem>
                                        <SelectItem value="private">Private</SelectItem>
                                        <SelectItem value="team">Team</SelectItem>
                                    </SelectContent>
                                </Select>

                                {hasFilters && (
                                    <Button variant="ghost" size="icon" onClick={clearFilters}>
                                        <X className="h-4 w-4" />
                                    </Button>
                                )}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                {/* Snippets List */}
                {snippets.data.length > 0 ? (
                    <div className="space-y-4">
                        {snippets.data.map((snippet) => (
                            <Card key={snippet.id} className="hover:bg-muted/50 transition-colors">
                                <CardContent className="pt-6">
                                    <div className="flex items-start justify-between gap-4">
                                        <div className="flex-1 space-y-2">
                                            <div className="flex items-center gap-2">
                                                <Link
                                                    href={`/snippets/${snippet.slug}`}
                                                    className="text-lg font-semibold hover:underline"
                                                >
                                                    {snippet.title}
                                                </Link>
                                                <Badge
                                                    variant={
                                                        snippet.visibility === 'public'
                                                            ? 'default'
                                                            : snippet.visibility === 'team'
                                                            ? 'secondary'
                                                            : 'outline'
                                                    }
                                                >
                                                    {snippet.visibility}
                                                </Badge>
                                            </div>

                                            {snippet.description && (
                                                <p className="text-sm text-muted-foreground line-clamp-2">
                                                    {snippet.description}
                                                </p>
                                            )}

                                            <div className="flex flex-wrap items-center gap-3 text-sm text-muted-foreground">
                                                {snippet.language && (
                                                    <span className="flex items-center gap-1">
                                                        <Code2 className="h-3 w-3" />
                                                        {snippet.language.name}
                                                    </span>
                                                )}
                                                <span className="flex items-center gap-1">
                                                    <Eye className="h-3 w-3" />
                                                    {snippet.views_count}
                                                </span>
                                                <span className="flex items-center gap-1">
                                                    <Star className="h-3 w-3" />
                                                    {snippet.favorites_count}
                                                </span>
                                                <span className="flex items-center gap-1">
                                                    <Clock className="h-3 w-3" />
                                                    {formatDistanceToNow(snippet.created_at)}
                                                </span>
                                            </div>

                                            {snippet.tags && snippet.tags.length > 0 && (
                                                <div className="flex flex-wrap gap-1">
                                                    {snippet.tags.map((tag) => (
                                                        <Badge key={tag.id} variant="outline" className="text-xs">
                                                            {tag.name}
                                                        </Badge>
                                                    ))}
                                                </div>
                                            )}
                                        </div>

                                        <div className="flex gap-2">
                                            <Link href={`/snippets/${snippet.slug}/edit`}>
                                                <Button variant="outline" size="sm">
                                                    Edit
                                                </Button>
                                            </Link>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        ))}

                        {/* Pagination */}
                        {snippets.links && snippets.links.length > 3 && (
                            <div className="flex items-center justify-center gap-2">
                                {snippets.links.map((link, index) => (
                                    <Link
                                        key={index}
                                        href={link.url || '#'}
                                        className={`px-3 py-2 text-sm rounded-md ${
                                            link.active
                                                ? 'bg-primary text-primary-foreground'
                                                : link.url
                                                ? 'hover:bg-muted'
                                                : 'text-muted-foreground cursor-not-allowed'
                                        }`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        )}
                    </div>
                ) : (
                    <Card>
                        <CardContent className="py-12">
                            <div className="text-center">
                                <Code2 className="mx-auto h-12 w-12 text-muted-foreground/50" />
                                <h3 className="mt-4 text-lg font-semibold">No snippets found</h3>
                                <p className="mt-2 text-sm text-muted-foreground">
                                    {hasFilters
                                        ? 'Try adjusting your filters or search terms.'
                                        : 'Get started by creating your first snippet.'}
                                </p>
                                {!hasFilters && (
                                    <Link href="/snippets/create">
                                        <Button className="mt-4">
                                            <Plus className="mr-2 h-4 w-4" />
                                            Create Snippet
                                        </Button>
                                    </Link>
                                )}
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </AppLayout>
    );
}

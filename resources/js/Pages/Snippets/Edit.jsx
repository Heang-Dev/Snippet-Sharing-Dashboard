import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { Textarea } from '@/Components/ui/textarea';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select';
import { ArrowLeft, Save, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { router } from '@inertiajs/react';

export default function SnippetsEdit({ snippet, languages, categories }) {
    const { data, setData, put, processing, errors } = useForm({
        title: snippet.title || '',
        description: snippet.description || '',
        code: snippet.code || '',
        language_id: snippet.language_id || '',
        category_id: snippet.category_id || '',
        visibility: snippet.visibility || 'private',
        file_name: snippet.file_name || '',
        tags: snippet.tags?.map((t) => t.name) || [],
        change_description: '',
    });

    const [tagInput, setTagInput] = useState('');

    const handleSubmit = (e) => {
        e.preventDefault();
        put(`/snippets/${snippet.slug}`);
    };

    const addTag = () => {
        const tag = tagInput.trim();
        if (tag && !data.tags.includes(tag)) {
            setData('tags', [...data.tags, tag]);
            setTagInput('');
        }
    };

    const removeTag = (tagToRemove) => {
        setData('tags', data.tags.filter((tag) => tag !== tagToRemove));
    };

    const handleTagKeyDown = (e) => {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag();
        }
    };

    const handleDelete = () => {
        if (confirm('Are you sure you want to delete this snippet? This action cannot be undone.')) {
            router.delete(`/snippets/${snippet.slug}`);
        }
    };

    return (
        <AppLayout title={`Edit: ${snippet.title}`}>
            <Head title={`Edit: ${snippet.title}`} />

            <div className="space-y-6">
                {/* Header */}
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href={`/snippets/${snippet.slug}`}>
                            <Button variant="ghost" size="icon">
                                <ArrowLeft className="h-4 w-4" />
                            </Button>
                        </Link>
                        <div>
                            <h2 className="text-2xl font-bold tracking-tight">Edit Snippet</h2>
                            <p className="text-muted-foreground">
                                Update your code snippet
                            </p>
                        </div>
                    </div>
                    <Button variant="destructive" size="sm" onClick={handleDelete}>
                        <Trash2 className="mr-2 h-4 w-4" />
                        Delete
                    </Button>
                </div>

                <form onSubmit={handleSubmit}>
                    <div className="grid gap-6 lg:grid-cols-3">
                        {/* Main Content */}
                        <div className="lg:col-span-2 space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Snippet Details</CardTitle>
                                    <CardDescription>
                                        Update the information about your code snippet
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="title">Title *</Label>
                                        <Input
                                            id="title"
                                            value={data.title}
                                            onChange={(e) => setData('title', e.target.value)}
                                            placeholder="Enter a descriptive title"
                                            className={errors.title ? 'border-destructive' : ''}
                                        />
                                        {errors.title && (
                                            <p className="text-sm text-destructive">{errors.title}</p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="description">Description</Label>
                                        <Textarea
                                            id="description"
                                            value={data.description}
                                            onChange={(e) => setData('description', e.target.value)}
                                            placeholder="What does this snippet do?"
                                            rows={3}
                                            className={errors.description ? 'border-destructive' : ''}
                                        />
                                        {errors.description && (
                                            <p className="text-sm text-destructive">{errors.description}</p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="file_name">File Name</Label>
                                        <Input
                                            id="file_name"
                                            value={data.file_name}
                                            onChange={(e) => setData('file_name', e.target.value)}
                                            placeholder="e.g., example.js"
                                        />
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Code *</CardTitle>
                                    <CardDescription>
                                        Edit your code snippet below
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Textarea
                                            id="code"
                                            value={data.code}
                                            onChange={(e) => setData('code', e.target.value)}
                                            placeholder="// Paste your code here..."
                                            rows={15}
                                            className={`font-mono text-sm ${errors.code ? 'border-destructive' : ''}`}
                                        />
                                        {errors.code && (
                                            <p className="text-sm text-destructive">{errors.code}</p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="change_description">Change Description</Label>
                                        <Input
                                            id="change_description"
                                            value={data.change_description}
                                            onChange={(e) => setData('change_description', e.target.value)}
                                            placeholder="Describe what you changed..."
                                        />
                                        <p className="text-xs text-muted-foreground">
                                            This will be recorded in the version history if the code changed.
                                        </p>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        {/* Sidebar */}
                        <div className="space-y-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle>Settings</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="space-y-2">
                                        <Label htmlFor="language">Language *</Label>
                                        <Select
                                            value={data.language_id}
                                            onValueChange={(value) => setData('language_id', value)}
                                        >
                                            <SelectTrigger className={errors.language_id ? 'border-destructive' : ''}>
                                                <SelectValue placeholder="Select language" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {languages.map((lang) => (
                                                    <SelectItem key={lang.id} value={lang.id}>
                                                        {lang.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        {errors.language_id && (
                                            <p className="text-sm text-destructive">{errors.language_id}</p>
                                        )}
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="category">Category</Label>
                                        <Select
                                            value={data.category_id || ''}
                                            onValueChange={(value) => setData('category_id', value)}
                                        >
                                            <SelectTrigger>
                                                <SelectValue placeholder="Select category" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {categories.map((cat) => (
                                                    <SelectItem key={cat.id} value={cat.id}>
                                                        {cat.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    </div>

                                    <div className="space-y-2">
                                        <Label htmlFor="visibility">Visibility *</Label>
                                        <Select
                                            value={data.visibility}
                                            onValueChange={(value) => setData('visibility', value)}
                                        >
                                            <SelectTrigger>
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="private">Private</SelectItem>
                                                <SelectItem value="public">Public</SelectItem>
                                                <SelectItem value="team">Team Only</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle>Tags</CardTitle>
                                    <CardDescription>
                                        Manage tags for your snippet
                                    </CardDescription>
                                </CardHeader>
                                <CardContent className="space-y-4">
                                    <div className="flex gap-2">
                                        <Input
                                            value={tagInput}
                                            onChange={(e) => setTagInput(e.target.value)}
                                            onKeyDown={handleTagKeyDown}
                                            placeholder="Add a tag..."
                                        />
                                        <Button type="button" variant="secondary" onClick={addTag}>
                                            Add
                                        </Button>
                                    </div>
                                    {data.tags.length > 0 && (
                                        <div className="flex flex-wrap gap-2">
                                            {data.tags.map((tag) => (
                                                <span
                                                    key={tag}
                                                    className="inline-flex items-center gap-1 rounded-md bg-secondary px-2 py-1 text-sm"
                                                >
                                                    {tag}
                                                    <button
                                                        type="button"
                                                        onClick={() => removeTag(tag)}
                                                        className="ml-1 hover:text-destructive"
                                                    >
                                                        ×
                                                    </button>
                                                </span>
                                            ))}
                                        </div>
                                    )}
                                </CardContent>
                            </Card>

                            <Button type="submit" className="w-full" disabled={processing}>
                                <Save className="mr-2 h-4 w-4" />
                                {processing ? 'Saving...' : 'Save Changes'}
                            </Button>
                        </div>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}

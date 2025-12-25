import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Toaster } from 'sonner';
import { ThemeProvider } from '@/contexts/ThemeContext';

const appName = import.meta.env.VITE_APP_NAME || 'Snippet Sharing';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx')
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <ThemeProvider>
                <App {...props} />
                <Toaster position="top-right" richColors />
            </ThemeProvider>
        );
    },
    progress: {
        color: '#84cc16', // Lime color for progress bar
    },
});

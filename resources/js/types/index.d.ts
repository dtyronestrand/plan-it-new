import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Calendar {
    id: number;
    name: string;
    color?: string;
    description?: string;
    user_id?: number;
    tasks: Task[];
}

export interface Task {
    id: number;
    name: string;
    done: boolean;
    notes?: string | null;
    calendar_id: number;
    due_date: string | null;
    sub_tasks: Task[];
    attachments: [];
    user_id?: number;
}

export type BreadcrumbItemType = BreadcrumbItem;

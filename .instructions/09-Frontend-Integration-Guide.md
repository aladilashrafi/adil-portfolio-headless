# Frontend Integration Guide

This guide explains how to connect your modern frontend (Next.js, Astro, React, etc.) to the Headless Portfolio CMS.

## 1. Prerequisites & CORS
Before your frontend can fetch data, you **must** whitelist your frontend domain:
1. Go to **Portfolio CMS > Settings > API & CORS**.
2. Add your frontend URL (e.g., `http://localhost:3000` or `https://your-portfolio.com`) to the **Allowed Origins** list.
3. Ensure **Enable REST API** and **Enable CORS** are both checked.

## 2. API Base URL
The standard base URL for all requests is:
`https://your-wordpress-site.com/wp-json/hpcms/v1`

## 3. TypeScript Interfaces
Copy these into your frontend project for type-safety:

```typescript
export interface HPCMSImage {
  id: number;
  url: string;
  thumbnail: string;
  alt: string;
}

export interface HPCMSProject {
  id: number;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  featuredImage: HPCMSImage | null;
  client: string;
  links: {
    live: string;
    github: string;
  };
  completionDate: string;
  featured: boolean;
  techStack: string[];
  gallery: string[];
  seo: {
    title: string;
    description: string;
  };
  technologies: string[];
  categories: string[];
  industries: string[];
  order: number;
  date: string;
}

export interface HPCMSSkill {
  id: number;
  title: string;
  slug: string;
  level: string;
  icon: string;
  experienceYears: number;
  officialUrl: string;
  categories: string[];
  order: number;
}

export interface HPCMSExperience {
  id: number;
  title: string;
  slug: string;
  role: string;
  company: string;
  companyUrl: string;
  employmentType: string;
  startDate: string;
  endDate: string;
  isCurrent: boolean;
  location: string;
  description: string;
  order: number;
}

export interface HPCMSEducation {
  id: number;
  title: string;
  slug: string;
  institution: string;
  degree: string;
  fieldOfStudy: string;
  startDate: string;
  endDate: string;
  grade: string;
  certificateUrl: string;
  description: string;
  order: number;
}

export interface HPCMSTestimonial {
  id: number;
  slug: string;
  quote: string;
  clientName: string;
  clientPosition: string;
  company: string;
  companyUrl: string;
  rating: number;
  clientImage: string;
  order: number;
}

export interface HPCMSProfile {
  full_name: string;
  tagline: string;
  bio: string;
  email: string;
  phone: string;
  location: string;
  social: {
    github: string;
    linkedin: string;
    twitter: string;
    youtube: string;
    behance: string;
  };
  seo: {
    meta_title: string;
    meta_description: string;
    og_image: string;
  };
}
```

## 4. Implementation Patterns

### Next.js (App Router - End-to-End)

#### 1. Setup Environment Variables
Create a `.env.local` file in your Next.js project:
```bash
NEXT_PUBLIC_API_URL=https://your-wordpress-site.com/wp-json/hpcms/v1
```

#### 2. Create an API Utility
```typescript
// lib/api.ts
const API_URL = process.env.NEXT_PUBLIC_API_URL;

export async function fetchAPI<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
  const res = await fetch(`${API_URL}${endpoint}`, {
    ...options,
    headers: { 'Content-Type': 'application/json', ...options.headers },
  });
  if (!res.ok) throw new Error(`Failed to fetch API: ${res.statusText}`);
  return res.json();
}
```

#### 3. List Page (e.g., Projects)
```typescript
// app/projects/page.tsx
import { fetchAPI } from '@/lib/api';
import { HPCMSProject } from '@/types';

export default async function ProjectsPage() {
  const projects = await fetchAPI<HPCMSProject[]>('/projects?sort=latest', {
    next: { revalidate: 3600 } // ISR: refresh every hour
  });

  return (
    <div>
      <h1>My Projects</h1>
      {projects.map(p => <ProjectCard key={p.id} project={p} />)}
    </div>
  );
}
```

#### 4. Dynamic Single Item Page (with SEO)
```typescript
// app/projects/[slug]/page.tsx
import { fetchAPI } from '@/lib/api';
import { Metadata } from 'next';

interface Props { params: { slug: string } }

// Generate SEO Metadata
export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const project = await fetchAPI<HPCMSProject>(`/projects/${params.slug}`);
  return {
    title: project.seo.title,
    description: project.seo.description,
    openGraph: { images: [project.featuredImage?.url || ''] }
  };
}

// Pre-generate paths for high performance (SSG)
export async function generateStaticParams() {
  const projects = await fetchAPI<HPCMSProject[]>('/projects');
  return projects.map((p) => ({ slug: p.slug }));
}

export default async function ProjectDetail({ params }: Props) {
  const project = await fetchAPI<HPCMSProject>(`/projects/${params.slug}`);
  return <article dangerouslySetInnerHTML={{ __html: project.content }} />
}
```

### React (Client Side with Fetch)
```typescript
import { useEffect, useState } from 'react';

export function SkillList() {
  const [skills, setSkills] = useState([]);

  useEffect(() => {
    fetch('https://api.site.com/wp-json/hpcms/v1/skills')
      .then(res => res.json())
      .then(data => setSkills(data));
  }, []);

  // ... render
}
```

## 5. Handling Images
The `featuredImage` object provides multiple sizes:
- `url`: Original full-size image.
- `thumbnail`: Square 150x150 thumbnail.

```jsx
{project.featuredImage && (
  <img 
    src={project.featuredImage.url} 
    alt={project.featuredImage.alt} 
    width="800"
    height="600"
  />
)}
```

## 6. Filtering & Sorting
Use query parameters to refine your results:
- **Featured Only**: `/projects?featured=1`
- **By Technology**: `/projects?technology=react` (uses the slug)
- **Sorting**: `/projects?sort=latest` (Options: `menu_order`, `latest`, `oldest`, `title`)
- **Pagination**: `/projects?page=1&per_page=6`

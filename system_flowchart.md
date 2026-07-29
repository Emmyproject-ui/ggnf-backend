# GGNF System Flow Chart — Frontend ↔ Backend

## Architecture Overview

```mermaid
flowchart TD
    User(["👤 User / Browser"])

    subgraph FRONTEND["🖥️ Next.js Frontend (localhost:3000)"]
        direction TB

        subgraph ROUTES["App Routes"]
            PUB["📂 Public Pages\n/(public)\n• Home\n• Projects\n• Blog\n• About\n• Contact"]
            AUTH["🔐 Auth Pages\n/auth\n• Sign In\n• Sign Up"]
            CUST["👤 Customer Portal\n/(customer)\n• Profile\n• Donations\n• Volunteering"]
            ADMIN["🛡️ Admin Panel\n/admin\n• Dashboard\n• Projects\n• Blog\n• Users\n• Volunteers\n• Contributions\n• Activity Logs\n• Security Logs"]
        end

        subgraph LIB["lib/ — API Layer"]
            AXIOS["axios.ts\nAxios Instance\n• baseURL from .env.local\n• 30s timeout\n• JSON headers"]
            INTERCEPTOR_REQ["Request Interceptor\n→ Inject Bearer Token\n→ Skip token on /login, /register"]
            INTERCEPTOR_RES["Response Interceptor\n→ 401 → redirect to /auth/signin\n→ 403, 404, 422, 500 handling\n→ ERR_NETWORK fallback"]
            API["api.ts\nService Methods\n• authApi\n• customerApi\n• adminApi\n• contactApi\n• blogApi\n• projectApi\n• settingsApi"]
        end

        subgraph SERVICES["services/storage"]
            TOKEN["token.service.ts\nLocalStorage\n• getToken()\n• setToken()\n• clearAll()"]
        end

        AXIOS --> INTERCEPTOR_REQ --> INTERCEPTOR_RES
        API --> AXIOS
        INTERCEPTOR_REQ -.-> TOKEN
    end

    subgraph BACKEND["⚙️ Laravel Backend (XAMPP / http://127.0.0.1/laravel/public/api)"]
        direction TB

        subgraph API_ROUTES["API Routes"]
            BAUTH["Auth\nPOST /login\nPOST /register\nPOST /logout\nGET  /me"]
            BPUB["Public\nGET /projects\nGET /blog\nGET /blog/:slug\nPOST /contact\nPOST /donate\nPOST /volunteer"]
            BCUST["Customer (Auth)\nGET /my-donations\nGET /my-volunteering\nPUT /profile\nPUT /password"]
            BADMIN["Admin (Auth + Role)\nGET  /admin/stats\nGET  /admin/users\nGET  /admin/donations\nGET  /admin/payments\nGET  /admin/contributions\nGET  /admin/volunteers\nPATCH /admin/volunteers/:id/approve|reject\nGET  /admin/messages\nGET  /admin/activity-logs\nGET  /admin/security-logs\nCRUD /admin/projects\nCRUD /admin/blog\nPOST /admin/upload\nGET  /settings/sessions\nDELETE /settings/sessions/:id"]
        end

        MIDDLEWARE["Middleware\n• Sanctum Token Auth\n• Role Check (admin)\n• CORS Headers"]
        DB[("🗄️ MySQL Database\n(XAMPP)")]

        MIDDLEWARE --> BAUTH & BCUST & BADMIN
        BAUTH & BPUB & BCUST & BADMIN --> DB
    end

    %% User Interactions
    User --> PUB & AUTH
    AUTH -->|"Sign in → store token"| TOKEN
    TOKEN -.->|"Read token"| INTERCEPTOR_REQ

    PUB -->|"getProjects()\ngetPosts()\nsendMessage()\ndonate()\nvolunteer()"| API
    AUTH -->|"login()\nregister()"| API
    CUST -->|"getMyDonations()\ngetMyVolunteering()\nupdateProfile()"| API
    ADMIN -->|"All admin CRUD calls"| API

    API -->|"HTTPS / HTTP Request\n+ Bearer Token"| MIDDLEWARE

    MIDDLEWARE -->|"JSON Response"| INTERCEPTOR_RES
    INTERCEPTOR_RES -->|"Success → data"| ROUTES
    INTERCEPTOR_RES -->|"401 → redirect"| AUTH

    %% Auth Gate
    CUST -.->|"Protected: checks token"| TOKEN
    ADMIN -.->|"Protected: checks token + role"| TOKEN
```

---

## Request Lifecycle (Step by Step)

```mermaid
sequenceDiagram
    actor User
    participant Page as Next.js Page
    participant API as lib/api.ts
    participant Axios as lib/axios.ts
    participant Token as token.service.ts
    participant Laravel as Laravel API
    participant DB as MySQL

    User->>Page: Visits /projects
    Page->>API: projectApi.getProjects()
    API->>Axios: GET /projects

    Axios->>Token: getToken()
    Token-->>Axios: Bearer <token> (or null)
    Axios->>Laravel: GET /api/projects\n+ Authorization: Bearer <token>

    Laravel->>DB: SELECT * FROM projects
    DB-->>Laravel: rows[]

    Laravel-->>Axios: 200 { data: [...] }
    Axios-->>API: res.data.data
    API-->>Page: projects[]
    Page-->>User: Renders project cards

    note over Axios,Laravel: ❌ If XAMPP is OFF → ERR_NETWORK\n→ catch block uses staticProjects fallback
```

---

## Authentication Flow

```mermaid
flowchart LR
    A(["User\nSign In"]) --> B["POST /api/login\n{email, password}"]
    B --> C{Laravel\nValidates}
    C -->|"✅ Valid"| D["Returns\n{token, user}"]
    C -->|"❌ Invalid"| E["401 Error\n'Invalid credentials'"]
    D --> F["token.service.ts\nstores token\nin localStorage"]
    F --> G["Redirect to\n/customer or /admin\nbased on role"]
    E --> H["Show error\non Sign In page"]

    G --> I["All subsequent requests\nauto-inject\nAuthorization: Bearer token"]
    I --> J{401 received?}
    J -->|"Yes"| K["clearAll()\nRedirect to\n/auth/signin?returnUrl=..."]
    J -->|"No"| L["Normal response"]
```

---

## Module Summary Table

| Layer | Location | Responsibility |
|-------|----------|----------------|
| **Pages (Public)** | `app/(public)/` | Home, Projects, Blog, About, Contact |
| **Pages (Auth)** | `app/auth/` | Sign In, Sign Up |
| **Pages (Customer)** | `app/(customer)/` | Donations, Volunteering, Profile |
| **Pages (Admin)** | `app/admin/` | Dashboard, CRUD for all resources |
| **API Services** | `lib/api.ts` | All typed API calls grouped by domain |
| **Axios Instance** | `lib/axios.ts` | Base URL, timeout, interceptors |
| **Token Service** | `services/storage/token.service.ts` | localStorage read/write/clear |
| **Static Fallbacks** | Inside page files | Used when backend is unreachable |
| **Laravel Backend** | `http://127.0.0.1/laravel/public/api` | REST API, Auth, CRUD, CORS |
| **Database** | MySQL via XAMPP | Persistent data storage |

# Job Board REST API Documentation

## Overview

The Job Board plugin provides a RESTful API for integrating external systems with the vacancy and application management system. This API follows REST conventions and returns JSON responses.

## Base URL

```
https://your-moodle-site.com/local/jobboard/api/v1/
```

## Authentication

All API requests require authentication using Bearer tokens.

### Obtaining a Token

API tokens are created by administrators through the Moodle admin interface:

1. Navigate to **Site Administration > Plugins > Local plugins > Job Board > Manage API Tokens**
2. Click **Create Token**
3. Set description, permissions, and optional restrictions
4. Copy the generated token (it will only be shown once)

### Using the Token

Include the token in the `Authorization` header:

```
Authorization: Bearer YOUR_API_TOKEN
```

## Rate Limiting

API requests are rate-limited to **100 requests per hour per token**.

Rate limit headers are included in all responses:

| Header | Description |
|--------|-------------|
| `X-RateLimit-Limit` | Maximum requests allowed per window |
| `X-RateLimit-Remaining` | Remaining requests in current window |
| `X-RateLimit-Reset` | Unix timestamp when the limit resets |

When rate limited, the API returns:

```json
{
  "error": true,
  "code": "rate_limit_exceeded",
  "message": "Rate limit exceeded. Please try again later.",
  "retry_after": 1800
}
```

## Response Format

### Success Response

```json
{
  "success": true,
  "data": { ... }
}
```

### Error Response

```json
{
  "error": true,
  "code": "error_code",
  "message": "Human readable error message"
}
```

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 201 | Created (for POST requests) |
| 400 | Bad Request - Invalid parameters |
| 401 | Unauthorized - Invalid or missing token |
| 403 | Forbidden - Token lacks required permission |
| 404 | Not Found - Resource doesn't exist |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error |

## Endpoints

### Vacancies

#### List Vacancies

```
GET /vacancies
```

**Permission required:** `view_vacancies`

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `status` | string | Filter by status (draft, published, closed, assigned) |
| `company_id` | int | Filter by company/tenant ID |
| `page` | int | Page number (default: 1) |
| `limit` | int | Results per page (default: 20, max: 100) |

**Response:**

```json
{
  "success": true,
  "data": {
    "vacancies": [
      {
        "id": 1,
        "code": "VAC2024-001",
        "title": "Software Developer",
        "contract_type": "catedra",
        "location": "Bogota",
        "department": "Engineering",
        "positions": 2,
        "status": "published",
        "open_date": "2024-01-15",
        "close_date": "2024-02-15",
        "created": "2024-01-10T10:00:00Z"
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 20,
      "total": 45,
      "total_pages": 3
    }
  }
}
```

#### Get Vacancy Details

```
GET /vacancies/{id}
```

**Permission required:** `view_vacancy_details`

**Response:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "code": "VAC2024-001",
    "title": "Software Developer",
    "description": "Full description of the vacancy...",
    "contract_type": "catedra",
    "duration": "6 months",
    "salary": "Competitive",
    "location": "Bogota",
    "department": "Engineering",
    "positions": 2,
    "requirements": "Requirements text...",
    "desirable": "Desirable qualifications...",
    "status": "published",
    "open_date": "2024-01-15",
    "close_date": "2024-02-15",
    "created": "2024-01-10T10:00:00Z",
    "required_documents": [
      {
        "code": "cedula",
        "name": "National ID",
        "required": true
      },
      {
        "code": "titulo_academico",
        "name": "Academic Degrees",
        "required": true
      }
    ]
  }
}
```

### Applications

#### List Applications

```
GET /applications
```

**Permission required:** `view_applications`

**Query Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `vacancy_id` | int | Filter by vacancy |
| `status` | string | Filter by status |
| `user_id` | int | Filter by applicant |
| `page` | int | Page number |
| `limit` | int | Results per page |

**Response:**

```json
{
  "success": true,
  "data": {
    "applications": [
      {
        "id": 1,
        "vacancy_id": 1,
        "vacancy_code": "VAC2024-001",
        "vacancy_title": "Software Developer",
        "user_id": 123,
        "applicant_name": "John Doe",
        "applicant_email": "john@example.com",
        "status": "submitted",
        "is_iser_exemption": false,
        "consent_given": true,
        "created": "2024-01-16T14:30:00Z"
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 20,
      "total": 150,
      "total_pages": 8
    }
  }
}
```

#### Get Application Details

```
GET /applications/{id}
```

**Permission required:** `view_application_details`

**Response:**

```json
{
  "success": true,
  "data": {
    "id": 1,
    "vacancy_id": 1,
    "vacancy_code": "VAC2024-001",
    "vacancy_title": "Software Developer",
    "user_id": 123,
    "applicant_name": "John Doe",
    "applicant_email": "john@example.com",
    "status": "submitted",
    "cover_letter": "Application cover letter...",
    "digital_signature": "John Doe",
    "is_iser_exemption": false,
    "exemption_reason": null,
    "consent_given": true,
    "consent_timestamp": "2024-01-16T14:30:00Z",
    "consent_ip": "192.168.1.100",
    "created": "2024-01-16T14:30:00Z",
    "documents": [
      {
        "id": 1,
        "type": "cedula",
        "type_name": "National ID",
        "filename": "cedula.pdf",
        "filesize": 524288,
        "validation_status": "approved",
        "uploaded": "2024-01-16T14:35:00Z"
      }
    ],
    "history": [
      {
        "from_status": null,
        "to_status": "submitted",
        "comments": null,
        "timestamp": "2024-01-16T14:30:00Z"
      }
    ]
  }
}
```

#### Create Application

```
POST /applications
```

**Permission required:** `create_application`

**Request Body:**

```json
{
  "vacancy_id": 1,
  "cover_letter": "Optional cover letter text",
  "digital_signature": "John Doe",
  "consent_given": true
}
```

**Response:**

```json
{
  "success": true,
  "data": {
    "id": 2,
    "vacancy_id": 1,
    "status": "submitted",
    "message": "Application submitted successfully"
  }
}
```

### Documents

#### Upload Document

```
POST /applications/{id}/documents
```

**Permission required:** `upload_documents`

**Content-Type:** `multipart/form-data`

**Form Parameters:**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `file` | file | Yes | The document file |
| `document_type` | string | Yes | Document type code (e.g., "cedula", "titulo_academico") |
| `issue_date` | string | No | Document issue date (YYYY-MM-DD) |

**Allowed File Types:**
- PDF: `application/pdf`
- JPEG: `image/jpeg`
- PNG: `image/png`

**Maximum File Size:** 5MB (configurable)

**Response:**

```json
{
  "success": true,
  "data": {
    "id": 5,
    "application_id": 1,
    "document_type": "cedula",
    "filename": "cedula.pdf",
    "filesize": 524288,
    "message": "Document uploaded successfully"
  }
}
```

## Permissions

API tokens can be configured with the following permissions:

| Permission | Description |
|------------|-------------|
| `view_vacancies` | List published vacancies |
| `view_vacancy_details` | View full vacancy details |
| `create_application` | Submit new applications |
| `view_applications` | List applications |
| `view_application_details` | View full application details |
| `upload_documents` | Upload documents to applications |
| `view_documents` | View/download application documents |

## Security Features

### IP Whitelist

Tokens can be restricted to specific IP addresses or CIDR ranges:

```
192.168.1.100
10.0.0.0/8
192.168.0.0/16
```

### Validity Period

Tokens can have:
- **Valid From**: Date when token becomes active
- **Valid Until**: Expiration date

### HTTPS Requirement

The API requires HTTPS connections. HTTP requests will be rejected with:

```json
{
  "error": true,
  "code": "https_required",
  "message": "HTTPS is required for API access"
}
```

## Code Examples

### PHP (cURL)

```php
<?php
$token = 'your_api_token_here';
$baseUrl = 'https://your-moodle-site.com/local/jobboard/api/v1';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $baseUrl . '/vacancies',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);
print_r($data);
```

### Python (requests)

```python
import requests

token = 'your_api_token_here'
base_url = 'https://your-moodle-site.com/local/jobboard/api/v1'

headers = {
    'Authorization': f'Bearer {token}',
    'Content-Type': 'application/json'
}

# List vacancies
response = requests.get(f'{base_url}/vacancies', headers=headers)
vacancies = response.json()

# Create application
application_data = {
    'vacancy_id': 1,
    'digital_signature': 'John Doe',
    'consent_given': True
}
response = requests.post(
    f'{base_url}/applications',
    headers=headers,
    json=application_data
)
result = response.json()
```

### JavaScript (fetch)

```javascript
const token = 'your_api_token_here';
const baseUrl = 'https://your-moodle-site.com/local/jobboard/api/v1';

const headers = {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json'
};

// List vacancies
fetch(`${baseUrl}/vacancies`, { headers })
    .then(response => response.json())
    .then(data => console.log(data));

// Upload document
const formData = new FormData();
formData.append('file', fileInput.files[0]);
formData.append('document_type', 'cedula');

fetch(`${baseUrl}/applications/1/documents`, {
    method: 'POST',
    headers: { 'Authorization': `Bearer ${token}` },
    body: formData
})
.then(response => response.json())
.then(data => console.log(data));
```

## Webhook Integration (Future)

Webhook support for event notifications is planned for a future release.

## Support

For API issues or questions, contact your Moodle administrator.

## Changelog

### v1.0.0 (Phase 5)
- Initial API release
- Endpoints: vacancies, applications, documents
- Token-based authentication
- Rate limiting
- IP whitelist support

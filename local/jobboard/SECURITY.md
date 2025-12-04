# Job Board Security Documentation

## Overview

The Job Board plugin implements comprehensive security measures to protect sensitive personal data, comply with Colombian Habeas Data regulations (Ley 1581/2012), and meet GDPR requirements.

## Table of Contents

1. [Data Protection](#data-protection)
2. [Authentication & Authorization](#authentication--authorization)
3. [API Security](#api-security)
4. [File Security](#file-security)
5. [Privacy Compliance](#privacy-compliance)
6. [Audit Logging](#audit-logging)
7. [Security Configuration](#security-configuration)
8. [Incident Response](#incident-response)

## Data Protection

### Encryption at Rest

The plugin supports optional AES-256-GCM encryption for uploaded documents:

- **Algorithm**: AES-256-GCM (Galois/Counter Mode)
- **Key Size**: 256 bits
- **IV**: 12 bytes, randomly generated per encryption
- **Authentication Tag**: 16 bytes

#### Enabling Encryption

1. Navigate to **Site Administration > Plugins > Local plugins > Job Board > Settings**
2. Enable "File encryption"
3. Generate or import an encryption key

**Warning**: Changing or losing the encryption key will make previously encrypted files unreadable.

#### Key Management

- Keys are stored securely in Moodle's configuration
- Keys should be backed up securely offline
- Key rotation requires re-encrypting all files

### Data Minimization

The plugin follows data minimization principles:
- Only required personal data is collected
- Document retention periods are enforced
- Automatic cleanup of expired data

### Secure File Storage

- Documents are stored in Moodle's secure file storage
- Files are not directly accessible via web URLs
- Access requires authentication and authorization checks

## Authentication & Authorization

### Moodle Authentication

The plugin leverages Moodle's authentication system:
- Session-based authentication for web interface
- All standard Moodle auth plugins supported
- MFA compatible (when configured in Moodle)

### Capability-Based Access Control

Fine-grained capabilities control access:

| Capability | Description |
|------------|-------------|
| `local/jobboard:apply` | Submit applications |
| `local/jobboard:viewownapplications` | View own applications |
| `local/jobboard:viewallapplications` | View all applications |
| `local/jobboard:reviewdocuments` | Review/validate documents |
| `local/jobboard:downloadanydocument` | Download any document |
| `local/jobboard:manageapitokens` | Manage API tokens |
| `local/jobboard:configure` | Configure system settings |

### Role-Based Access

Recommended role configurations:

**Applicant (Authenticated User)**
- `local/jobboard:apply`
- `local/jobboard:viewownapplications`

**HR Reviewer**
- `local/jobboard:viewallapplications`
- `local/jobboard:reviewdocuments`

**HR Manager**
- All reviewer capabilities
- `local/jobboard:downloadanydocument`
- `local/jobboard:manageworkflow`

**Administrator**
- All capabilities

## API Security

### Token-Based Authentication

API access uses secure bearer tokens:

```
Authorization: Bearer <token>
```

Token security features:
- **Secure Generation**: 32 bytes of cryptographically secure random data
- **Secure Storage**: SHA-256 hashed before storage
- **One-Time Display**: Raw token shown only at creation

### Rate Limiting

Protection against abuse:
- Default: 100 requests per hour per token
- Configurable per installation
- Rate limit headers in responses
- 429 response when exceeded

### IP Whitelisting

Restrict token usage by IP:
- Individual IP addresses
- CIDR notation (e.g., `192.168.1.0/24`)
- IPv4 supported

### Token Validity Periods

Time-based restrictions:
- **Valid From**: Token activation date
- **Valid Until**: Token expiration date
- Automatic rejection of expired tokens

### Permission Scoping

Tokens are scoped to specific permissions:
- Principle of least privilege
- Grant only required permissions
- Separate tokens for different integrations

### HTTPS Requirement

API endpoints require HTTPS:
- HTTP requests are rejected
- HSTS header recommended
- TLS 1.2+ required

### Security Headers

API responses include:
- `X-Content-Type-Options: nosniff`
- `X-Frame-Options: DENY`
- `X-XSS-Protection: 1; mode=block`
- Cache-Control: No caching of sensitive data

## File Security

### Upload Validation

All uploads are validated:

1. **MIME Type Check**: Verified against allowed types
2. **File Extension Check**: Must match content type
3. **File Size Limit**: Configurable maximum size
4. **Virus Scanning**: Integrates with ClamAV if configured

### Allowed File Types

Default allowed formats:
- PDF (`application/pdf`)
- JPEG (`image/jpeg`)
- PNG (`image/png`)

### Document Integrity

- SHA-1 content hash stored for integrity verification
- Superseded documents are marked, not deleted
- Version history maintained

## Privacy Compliance

### GDPR Compliance

The plugin implements Moodle's Privacy API:

1. **Right to Access**: Users can export their data
2. **Right to Erasure**: Users can request deletion
3. **Data Portability**: Export in JSON or PDF format
4. **Consent Recording**: Explicit consent with timestamp and IP

### Colombian Habeas Data (Ley 1581/2012)

Specific compliance features:
- Informed consent before data collection
- Clear privacy policy presentation
- Data processing purpose disclosure
- Right to update or correct data

### Consent Management

Consent is recorded with:
- User ID
- Timestamp
- IP address
- Consent text version
- Digital signature

### Data Export

Users can export:
- Personal information
- All applications
- Uploaded documents metadata
- Consent records
- API token information (without actual tokens)

Export formats:
- JSON (machine-readable)
- PDF (human-readable)

### Data Deletion

Deletion process:
1. User requests deletion
2. Administrator reviews request
3. All user data permanently removed
4. Files deleted from storage
5. Audit log retained (anonymized)

### Data Retention

Automatic cleanup of:
- Rejected/withdrawn applications (configurable days)
- Expired API tokens
- Old audit logs (configurable)
- Old notifications

Default retention: 730 days (2 years)

## Audit Logging

### Logged Events

All significant actions are logged:

| Category | Events |
|----------|--------|
| Vacancies | Create, update, delete, publish |
| Applications | Submit, status changes, withdraw |
| Documents | Upload, validate, reject, download |
| API | Token created, revoked, used |
| Administration | Configuration changes |

### Audit Record Format

Each audit entry includes:
- User ID
- Action type
- Entity type and ID
- Additional data (JSON)
- IP address
- User agent
- Timestamp

### Audit Log Access

- Only administrators can view audit logs
- Logs are searchable and filterable
- Export available for compliance

### Audit Log Retention

- Configurable retention period
- Anonymization option for old logs
- Automatic cleanup via scheduled task

## Security Configuration

### Recommended Settings

```php
// config.php additions for enhanced security

// Force HTTPS
$CFG->sslproxy = true;

// Session security
$CFG->cookiesecure = true;
$CFG->cookiehttponly = true;

// Password policy
$CFG->passwordpolicy = true;
$CFG->minpasswordlength = 12;
```

### Plugin Settings

Navigate to: **Site Administration > Plugins > Local plugins > Job Board > Settings**

| Setting | Recommendation |
|---------|----------------|
| Enable encryption | Yes (for sensitive documents) |
| Enable REST API | Only if needed |
| Rate limit | 100 or lower |
| Data retention | Based on legal requirements |
| Max file size | 5MB or lower |

### Server Security

Recommendations:
- Keep Moodle and PHP updated
- Use Web Application Firewall (WAF)
- Enable ClamAV antivirus scanning
- Regular security audits
- Monitor access logs

## Incident Response

### Security Incident Types

1. **Data Breach**: Unauthorized access to personal data
2. **API Abuse**: Excessive or malicious API usage
3. **Document Leak**: Unauthorized document access
4. **Account Compromise**: User account takeover

### Response Procedures

1. **Identify**: Detect and confirm the incident
2. **Contain**: Revoke tokens, disable accounts
3. **Investigate**: Review audit logs
4. **Notify**: Inform affected users (if required by law)
5. **Remediate**: Fix vulnerabilities
6. **Document**: Record incident details

### Emergency Actions

**Revoke All API Tokens:**
```sql
UPDATE {local_jobboard_api_token} SET enabled = 0;
```

**Disable API:**
Set `api_enabled` to 0 in plugin settings.

**Lock User Account:**
Use Moodle's user suspension feature.

### Reporting Security Issues

Report security vulnerabilities to:
- Moodle security team (for core issues)
- Plugin maintainers (for plugin issues)

Do not disclose publicly until fixed.

## Security Checklist

### Installation

- [ ] HTTPS configured and enforced
- [ ] Strong admin password set
- [ ] Unnecessary capabilities revoked
- [ ] API disabled if not needed
- [ ] File upload limits configured

### Regular Maintenance

- [ ] Review audit logs weekly
- [ ] Revoke unused API tokens
- [ ] Update Moodle and plugins
- [ ] Backup encryption keys
- [ ] Test data export/deletion

### Compliance

- [ ] Privacy policy up to date
- [ ] Consent text reviewed
- [ ] Data retention policy configured
- [ ] Annual privacy audit

## Contact

For security concerns regarding this plugin:
- Review Moodle security guidelines
- Contact your institution's security team
- Report plugin issues to maintainers

---

*Last updated: December 2024*
*Plugin version: 1.4.0*

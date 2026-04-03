# DPASS-REST API Complete Analysis Report

This document provides an in-depth analysis of the source code from the [DPASS-REST repository](https://github.com/weng-pang/DPASS-REST), summarizing all available RESTful API endpoints along with request and response examples.

## 1. System Architecture & Global Conventions

DPASS-REST is a lightweight PHP RESTful API service built on the Slim Framework. The system is primarily designed to handle attendance and access control records, providing functionalities to query, add, and revoke entries.

**Global API Conventions:**
*   **Data Format**: All requests and responses use the `application/json` format.
*   **Authentication**: Except for the public version endpoint, all POST requests require API Key authentication by including a `key` in the URL parameters (e.g., `?key=YOUR_API_KEY`). If the key is invalid, expired, or revoked, the system returns an HTTP 403 error.
*   **Payload Encapsulation**: For POST requests, the actual business data must be passed as a JSON string encapsulated within a form parameter or JSON key named `content` (Slim's `$app->request()->params('content')` automatically parses this based on the client's Content-Type).

---

## 2. API Endpoint Overview

The system provides the following primary endpoints:

| HTTP Method | Endpoint Path | Auth Required | Description |
| :--- | :--- | :--- | :--- |
| GET | `/version` | No | Retrieve system version and Git commit information |
| POST | `/add` | Yes | Add a single record |
| POST | `/addbatch` | Yes | Add multiple records in a batch |
| POST | `/find` | Yes | Query records based on specific criteria |
| POST | `/revoke` | Yes | Revoke (soft-delete) a specific record |
| POST | `/check` | Yes | Check the latest update time for each machine |
| POST | `/check_profile` | Yes | Check the latest record time for staff members |
| POST | `/approve` | Yes | Approve a record (currently not fully implemented) |
| POST | `/disapprove` | Yes | Revoke an approval (currently not fully implemented) |

---

## 3. Detailed Endpoint Descriptions & Examples

### 3.1 Get System Version

**Endpoint:** `GET /version`

**Description:** Retrieves the current API system version and commit information. No API Key is required.

**Request Example:**
```http
GET /version HTTP/1.1
Host: your-api-domain.com
```

**Response Example:**
```json
{
    "version": "1.3",
    "commit": "a1b2c3d"
}
```

### 3.2 Add Single Record

**Endpoint:** `POST /add?key=YOUR_API_KEY`

**Description:** Adds a single attendance or access record. The data must conform to specific formats, such as IP addresses matching a regex, and maximum value limits for ID and machine ID.

**Request Parameters (`content` payload):**
*   `id` (Integer): User or card ID (maximum 9999)
*   `dateTime` (String): Record time in `YYYY-MM-DD HH:mm:ss` format
*   `machineId` (Integer): Machine ID (maximum 999)
*   `entryId` (Integer): Entry point ID (maximum 6)
*   `ipAddress` (String): IPv4 address of the device
*   `portNumber` (Integer): Port number of the device

**Request Example:**
```http
POST /add?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
Content-Type: application/x-www-form-urlencoded

content={"id":123,"dateTime":"2023-10-01 08:30:00","machineId":1,"entryId":2,"ipAddress":"192.168.1.100","portNumber":8080}
```

**Success Response Example:**
```json
{
    "transactionId": 105
}
```

### 3.3 Batch Add Records

**Endpoint:** `POST /addbatch?key=YOUR_API_KEY`

**Description:** Adds multiple records in a single request. If the system is configured with a specific `BATCH_SIZE`, the array length must match exactly (the default is 0, meaning unlimited).

**Request Parameters (`content` payload):**
A JSON array containing multiple single-record objects.

**Request Example:**
```http
POST /addbatch?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
Content-Type: application/x-www-form-urlencoded

content=[{"id":123,"dateTime":"2023-10-01 08:30:00","machineId":1,"entryId":2,"ipAddress":"192.168.1.100","portNumber":8080},{"id":124,"dateTime":"2023-10-01 08:35:00","machineId":1,"entryId":2,"ipAddress":"192.168.1.100","portNumber":8080}]
```

**Success Response Example:**
```json
[
    {"transactionId": 106},
    {"transactionId": 107}
]
```

### 3.4 Find Records

**Endpoint:** `POST /find?key=YOUR_API_KEY`

**Description:** Queries records based on conditions. At least one query criterion must be provided; otherwise, it throws an `Insufficient Query Content` error. Unprovided conditions are treated as a full range (wildcard).

**Request Parameters (`content` payload):**
*   `id` (Integer, optional): Query a specific user ID
*   `machineId` (Integer, optional): Query a specific machine ID
*   `startTime` (String, optional): Start time
*   `endTime` (String, optional): End time

**Request Example:**
```http
POST /find?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
Content-Type: application/x-www-form-urlencoded

content={"id":123,"startTime":"2023-10-01 00:00:00","endTime":"2023-10-31 23:59:59"}
```

**Success Response Example:**
```json
[
    {
        "serial": 105,
        "id": 123,
        "datetime": "2023-10-01 08:30:00",
        "machineid": 1,
        "entryid": 2,
        "ipaddress": "192.168.1.100",
        "portnumber": 8080,
        "update": "2023-10-01 08:30:01",
        "key": "dummy-api-key",
        "revoked": 0
    },
    {
        "serial": 106,
        "id": 123,
        "datetime": "2023-10-02 09:05:00",
        "machineid": 1,
        "entryid": 3,
        "ipaddress": "192.168.1.100",
        "portnumber": 8080,
        "update": "2023-10-02 09:05:02",
        "key": "dummy-api-key",
        "revoked": 0
    },
    {
        "serial": 112,
        "id": 123,
        "datetime": "2023-10-15 17:45:00",
        "machineid": 2,
        "entryid": 1,
        "ipaddress": "192.168.1.101",
        "portnumber": 8080,
        "update": "2023-10-15 17:45:03",
        "key": "dummy-api-key",
        "revoked": 0
    }
]
```

### 3.5 Revoke Record

**Endpoint:** `POST /revoke?key=YOUR_API_KEY`

**Description:** Marks a record as revoked (`revoked = 1`). This acts as a soft-delete mechanism.

**Request Parameters (`content` payload):**
*   `serial` (Integer): The transaction ID (serial number) of the record to revoke

**Request Example:**
```http
POST /revoke?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
Content-Type: application/x-www-form-urlencoded

content={"serial":105}
```

**Success Response Example:**
```json
{
    "serial": 105
}
```

### 3.6 Check Machine Update Status

**Endpoint:** `POST /check?key=YOUR_API_KEY`

**Description:** Retrieves the last update time for each machine. Used for auditing and verifying data synchronization status.

**Request Example:**
```http
POST /check?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
```

**Success Response Example:**
```json
[
    {
        "machineid": 1,
        "update": "2023-10-01 08:35:01"
    },
    {
        "machineid": 2,
        "update": "2023-09-28 17:00:00"
    }
]
```

### 3.7 Check Latest Staff Record

**Endpoint:** `POST /check_profile?key=YOUR_API_KEY`

**Description:** Retrieves the latest record time for each staff member (ID <= 999).

**Request Example:**
```http
POST /check_profile?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
```

**Success Response Example:**
```json
[
    {
        "id": 123,
        "update": "2023-10-01 08:30:01",
        "machineid": 1
    }
]
```

### 3.8 Approval Features (Unimplemented)

**Endpoints:** `POST /approve?key=YOUR_API_KEY` and `POST /disapprove?key=YOUR_API_KEY`

**Description:** These two routes are declared in `RecordController.php`, but the functions in the controller are empty. Although `approve` and `disapprove` methods are defined within `Model/Record.php`, the controller does not call them correctly. Currently, these endpoints have no actual effect; calling them will not trigger any action or throw an exception.

---

## 4. Error Handling

When an error occurs (such as invalid parameters or incorrect API Key), the API returns a non-200 HTTP status code (e.g., 400 Bad Request or 403 Forbidden) along with a standardized error JSON.

**Error Response Example:**
```json
{
    "error": {
        "procedure": "add record",
        "text": "Incorrect IP Address:999.999.999.999"
    }
}
```

## References
[1] DPASS-REST Source Code: https://github.com/weng-pang/DPASS-REST

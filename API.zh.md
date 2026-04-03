# DPASS-REST API 完整分析報告

本文件針對 [DPASS-REST 儲存庫](https://github.com/weng-pang/DPASS-REST) 的原始碼進行了深入分析，總結出所有可用的 RESTful API 接口，並附上請求與回應的範例。

## 1. 系統架構與共用規範

DPASS-REST 是一個基於 Slim Framework 構建的 PHP 輕量級 RESTful API 服務。該系統主要用於處理打卡、門禁等紀錄（Records），並提供查詢、新增與撤銷等功能。

**全局 API 規範：**
*   **傳輸格式**：所有請求和回應均使用 `application/json` 格式。
*   **認證機制**：除了查詢版本號的公開接口外，所有的 POST 請求都必須在 URL 參數中攜帶 `key` 進行 API Key 認證（例如：`?key=YOUR_API_KEY`）。如果 Key 無效、過期或被撤銷，系統會回傳 HTTP 403 錯誤。
*   **資料封裝**：對於 POST 請求，實際的業務資料必須作為一個 JSON 字符串封裝在名為 `content` 的表單參數或 JSON 鍵中傳遞（取決於客戶端請求的 Content-Type，Slim 的 `$app->request()->params('content')` 會自動解析）。

---

## 2. API 接口總覽

系統提供了以下幾個主要端點：

| HTTP 方法 | 端點路徑 | 認證要求 | 功能描述 |
| :--- | :--- | :--- | :--- |
| GET | `/version` | 否 | 取得系統版本號與 Git Commit 資訊 |
| POST | `/add` | 是 | 新增單筆紀錄 |
| POST | `/addbatch` | 是 | 批量新增多筆紀錄 |
| POST | `/find` | 是 | 依條件查詢紀錄 |
| POST | `/revoke` | 是 | 撤銷（標記刪除）指定的紀錄 |
| POST | `/check` | 是 | 檢查各機器的最新更新時間 |
| POST | `/check_profile` | 是 | 檢查員工的最新紀錄時間 |
| POST | `/approve` | 是 | 批准紀錄（目前尚未完整實作） |
| POST | `/disapprove` | 是 | 撤銷批准（目前尚未完整實作） |

---

## 3. 接口詳細說明與範例

### 3.1 取得系統版本

**端點：** `GET /version`

**描述：** 取得當前 API 系統的版本與 commit 資訊。不需要 API Key。

**請求範例：**
```http
GET /version HTTP/1.1
Host: your-api-domain.com
```

**回應範例：**
```json
{
    "version": "1.3",
    "commit": "a1b2c3d"
}
```

### 3.2 新增單筆紀錄

**端點：** `POST /add?key=YOUR_API_KEY`

**描述：** 新增一筆打卡或門禁紀錄。資料必須符合格式，例如 IP 地址需符合正則表達式，ID 和機器 ID 有最大值限制。

**請求參數 (`content` 內容)：**
*   `id` (Integer): 使用者或卡片 ID（最大 9999）
*   `dateTime` (String): 紀錄時間，格式為 `YYYY-MM-DD HH:mm:ss`
*   `machineId` (Integer): 機器 ID（最大 999）
*   `entryId` (Integer): 進入點 ID（最大 6）
*   `ipAddress` (String): 設備的 IPv4 地址
*   `portNumber` (Integer): 設備的連接埠號

**請求範例：**
```http
POST /add?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
Content-Type: application/x-www-form-urlencoded

content={"id":123,"dateTime":"2023-10-01 08:30:00","machineId":1,"entryId":2,"ipAddress":"192.168.1.100","portNumber":8080}
```

**成功回應範例：**
```json
{
    "transactionId": 105
}
```

### 3.3 批量新增紀錄

**端點：** `POST /addbatch?key=YOUR_API_KEY`

**描述：** 一次性新增多筆紀錄。如果系統配置了 `BATCH_SIZE`，則陣列長度必須完全符合該設定（目前預設為 0，表示無限制）。

**請求參數 (`content` 內容)：**
一個包含多個單筆紀錄物件的 JSON 陣列。

**請求範例：**
```http
POST /addbatch?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
Content-Type: application/x-www-form-urlencoded

content=[{"id":123,"dateTime":"2023-10-01 08:30:00","machineId":1,"entryId":2,"ipAddress":"192.168.1.100","portNumber":8080},{"id":124,"dateTime":"2023-10-01 08:35:00","machineId":1,"entryId":2,"ipAddress":"192.168.1.100","portNumber":8080}]
```

**成功回應範例：**
```json
[
    {"transactionId": 106},
    {"transactionId": 107}
]
```

### 3.4 查詢紀錄

**端點：** `POST /find?key=YOUR_API_KEY`

**描述：** 根據條件查詢紀錄。至少需要提供一個查詢條件，否則會報錯 `Insufficient Query Content`。未提供的條件視為全範圍（Wildcard）。

**請求參數 (`content` 內容)：**
*   `id` (Integer, 選填): 查詢特定的使用者 ID
*   `machineId` (Integer, 選填): 查詢特定的機器 ID
*   `startTime` (String, 選填): 起始時間
*   `endTime` (String, 選填): 結束時間

**請求範例：**
```http
POST /find?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
Content-Type: application/x-www-form-urlencoded

content={"id":123,"startTime":"2023-10-01 00:00:00","endTime":"2023-10-31 23:59:59"}
```

**成功回應範例：**
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
    }
]
```

### 3.5 撤銷紀錄

**端點：** `POST /revoke?key=YOUR_API_KEY`

**描述：** 將一筆紀錄標記為已撤銷（`revoked = 1`）。這是一種軟刪除機制。

**請求參數 (`content` 內容)：**
*   `serial` (Integer): 要撤銷的紀錄流水號 (Transaction ID)

**請求範例：**
```http
POST /revoke?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
Content-Type: application/x-www-form-urlencoded

content={"serial":105}
```

**成功回應範例：**
```json
{
    "serial": 105
}
```

### 3.6 檢查機器更新狀態

**端點：** `POST /check?key=YOUR_API_KEY`

**描述：** 獲取每個機器的最後更新時間。用於審計和確認資料同步狀態。

**請求範例：**
```http
POST /check?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
```

**成功回應範例：**
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

### 3.7 檢查員工最新紀錄

**端點：** `POST /check_profile?key=YOUR_API_KEY`

**描述：** 獲取每位員工（ID <= 999）的最新紀錄時間。

**請求範例：**
```http
POST /check_profile?key=dummy-api-key HTTP/1.1
Host: your-api-domain.com
```

**成功回應範例：**
```json
[
    {
        "id": 123,
        "update": "2023-10-01 08:30:01",
        "machineid": 1
    }
]
```

### 3.8 審批功能 (未實作完成)

**端點：** `POST /approve?key=YOUR_API_KEY` 和 `POST /disapprove?key=YOUR_API_KEY`

**描述：** 在 `RecordController.php` 中有宣告這兩個路由，但在控制器中的函數是空的。雖然 `Model/Record.php` 內有 `approve` 和 `disapprove` 方法的定義，但由於控制器未正確呼叫，目前這兩個端點沒有實際作用，調用將不會有任何反應或拋出異常。

---

## 4. 錯誤處理

當發生錯誤（如參數不合法、API Key 錯誤等）時，API 會回傳非 200 的 HTTP 狀態碼（例如 400 Bad Request 或 403 Forbidden），並附帶標準化的錯誤 JSON。

**錯誤回應範例：**
```json
{
    "error": {
        "procedure": "add record",
        "text": "Incorrect IP Address:999.999.999.999"
    }
}
```

## 參考文獻
[1] DPASS-REST 原始碼: https://github.com/weng-pang/DPASS-REST

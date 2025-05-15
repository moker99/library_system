# 📚 書櫃系統技術架構與啟動說明
## 🔧 技術架構

本模組採用前後分離架構，整體開發技術如下：

### ✅ 前端技術（Vue 3 + Vite）
- **Vue 3** 搭配 Composition API
- **Vite** 為開發與建構工具
- **Tailwind CSS** 作為設計系統
- **Axios** 用於串接後端 API
- **SweetAlert2 (Swal)** 用於使用者操作回饋
- **Vue Draggable Next** 用於拖曳排序

### ✅ 後端技術（Laravel 8）
- RESTful API 實作
- 對應 `/api/shelves`, `/api/books` 等資源
- 包含「最佳儲位演算法」用於決定書本自動放置位置

---

## 🚀 啟動專案流程

### 1. 安裝後端 Laravel 8 專案

composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

> 伺服器預設啟動於 `http://localhost:8000`

### 2. 安裝前端 Vue 3 專案（Vite）

npm install
npm run dev

> 伺服器預設啟動於 `http://localhost:5173`
> 開發環境啟動於 `http://localhost:5173`

前端透過 `vite.config.js` 中的 `server.proxy` 設定 API 轉發：
server: {
    proxy: {
        '/api': {
        target: 'http://127.0.0.1:8000',
        changeOrigin: true,
        }
    }
}
---

🧩 功能總覽
📖 書櫃系統（BookShelfMain.vue, BookShelfPage.vue）

    可檢視所有書櫃資訊（名稱、簡稱、總容量、目前書籍數）

    書櫃支援拖曳排序（Drag & Drop）

    點擊書櫃可進入細部頁面，查看各樓層書本清單

    可新增書櫃，並自訂樓層數與每層容量

📦 書本新增功能 BookShelfMain.vue, BookShelfPage.vue）

    可在指定書櫃的特定樓層中新增書本

    若不指定樓層，系統會自動依最佳儲位順序進行配置（先書櫃、後樓層、最後空位）

    書籍卡片採動態背景色，方便視覺辨識

🔍 書本搜尋（SearchPage.vue）

    提供獨立頁面，可同時查詢最多三筆書籍

    支援書名／ISBN 模糊比對

    回傳包含「書櫃名稱、樓層編號、儲位位置」

    結果依最佳取書順序排序（書櫃 → 樓層 → 書格）

---

## 🗂 組件結構簡介

| 檔案名稱 | 說明 |
|-----------|------|
| `BookShelfPage.vue` | 單一書櫃顯示與書籍管理主頁 |
| `BookModal.vue`     | 共用的新增書本彈窗元件 |
| `BookShelfMain.vue` | 書櫃總覽與排序控制頁面（含書櫃新增、書本最佳位置新增功能） |
| `SearchPage.vue`    | 書籍查詢定位頁面（依書櫃 → 樓層 → 位置排序結果） |
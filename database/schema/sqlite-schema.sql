CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "phone" varchar,
  "role" varchar check("role" in('customer', 'seller', 'admin')) not null default 'customer',
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_expiration_index" on "cache"("expiration");
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_locks_expiration_index" on "cache_locks"("expiration");
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "categories"(
  "id" integer primary key autoincrement not null,
  "name_ar" varchar not null,
  "name_en" varchar not null,
  "slug" varchar not null,
  "icon" varchar,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "categories_slug_unique" on "categories"("slug");
CREATE TABLE IF NOT EXISTS "stores"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "name" varchar not null,
  "store_name" varchar,
  "store_type" varchar,
  "slug" varchar not null,
  "description" text,
  "logo" varchar,
  "cover" varchar,
  "cover_image" varchar,
  "contact_info" text,
  "kyc_status" varchar check("kyc_status" in('pending', 'approved', 'rejected')) not null default 'pending',
  "status" varchar check("status" in('pending', 'active', 'inactive', 'suspended')) not null default 'pending',
  "views" integer not null default '0',
  "verified_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  "whatsapp_number" varchar,
  "location" varchar,
  "product_types" varchar,
  "identity_image" varchar,
  "owner_name" varchar,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "stores_slug_unique" on "stores"("slug");
CREATE TABLE IF NOT EXISTS "favorites"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "product_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "products"(
  "id" integer primary key autoincrement not null,
  "store_id" integer not null,
  "category_id" integer,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "price" numeric not null,
  "images" text,
  "status" varchar check("status" in('active', 'hidden', 'available', 'unavailable', 'deleted')) not null default 'active',
  "views" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("store_id") references "stores"("id") on delete cascade,
  foreign key("category_id") references "categories"("id") on delete set null
);
CREATE UNIQUE INDEX "products_slug_unique" on "products"("slug");
CREATE TABLE IF NOT EXISTS "followers"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "store_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("store_id") references "stores"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sales_logs"(
  "id" integer primary key autoincrement not null,
  "store_id" integer not null,
  "product_id" integer not null,
  "customer_id" integer,
  "customer_contact" varchar,
  "status" varchar check("status" in('pending', 'confirmed', 'cancelled')) not null default 'pending',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("store_id") references "stores"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade,
  foreign key("customer_id") references "users"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "product_reviews"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "product_id" integer not null,
  "rating" integer not null,
  "comment" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("product_id") references "products"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "store_reviews"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "store_id" integer not null,
  "rating" integer not null,
  "comment" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("store_id") references "stores"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "ads"(
  "id" integer primary key autoincrement not null,
  "store_id" integer,
  "banner_image" varchar not null,
  "link" varchar,
  "status" varchar check("status" in('pending', 'active', 'expired')) not null default 'pending',
  "start_date" datetime,
  "end_date" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("store_id") references "stores"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "notifications"(
  "id" varchar not null,
  "type" varchar not null,
  "notifiable_type" varchar not null,
  "notifiable_id" integer not null,
  "data" text not null,
  "read_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  primary key("id")
);
CREATE INDEX "notifications_notifiable_type_notifiable_id_index" on "notifications"(
  "notifiable_type",
  "notifiable_id"
);
CREATE TABLE IF NOT EXISTS "settings"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "value" text,
  "type" varchar not null default 'string',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "settings_key_unique" on "settings"("key");
CREATE TABLE IF NOT EXISTS "personal_access_tokens"(
  "id" integer primary key autoincrement not null,
  "tokenable_type" varchar not null,
  "tokenable_id" integer not null,
  "name" text not null,
  "token" varchar not null,
  "abilities" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens"(
  "tokenable_type",
  "tokenable_id"
);
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" on "personal_access_tokens"(
  "token"
);
CREATE INDEX "personal_access_tokens_expires_at_index" on "personal_access_tokens"(
  "expires_at"
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2026_02_27_213905_create_categories_table',1);
INSERT INTO migrations VALUES(5,'2026_02_27_213905_create_stores_table',1);
INSERT INTO migrations VALUES(6,'2026_02_27_213906_create_favorites_table',1);
INSERT INTO migrations VALUES(7,'2026_02_27_213906_create_products_table',1);
INSERT INTO migrations VALUES(8,'2026_02_27_213907_create_followers_table',1);
INSERT INTO migrations VALUES(9,'2026_02_27_213907_create_sales_logs_table',1);
INSERT INTO migrations VALUES(10,'2026_02_27_213908_create_product_reviews_table',1);
INSERT INTO migrations VALUES(11,'2026_02_27_213908_create_store_reviews_table',1);
INSERT INTO migrations VALUES(12,'2026_02_27_213909_create_ads_table',1);
INSERT INTO migrations VALUES(13,'2026_02_27_213909_create_notifications_table',1);
INSERT INTO migrations VALUES(14,'2026_02_27_213909_create_settings_table',1);
INSERT INTO migrations VALUES(15,'2026_02_28_112031_create_personal_access_tokens_table',1);
INSERT INTO migrations VALUES(16,'2026_02_28_205627_add_kyc_details_to_stores_table',2);
INSERT INTO migrations VALUES(17,'2026_03_01_013509_add_owner_name_to_stores_table',3);
INSERT INTO migrations VALUES(18,'2026_03_01_030613_add_cover_and_views_to_stores_table',4);

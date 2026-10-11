-- =====================================================================
-- Archer Monitor - Seed RBAC (roles, route_permissions, menus)
-- Dari config/rbac.php. Jalankan SETELAH schema.sql. Idempotent.
-- =====================================================================

SET NAMES utf8mb4;
START TRANSACTION;

INSERT INTO `roles` (`code`,`name`,`is_staff`,`sort`,`created_at`,`updated_at`) VALUES
  ('admin','Administrator',1,0,NOW(),NOW()),
  ('coach','Pelatih',1,1,NOW(),NOW()),
  ('athlete','Atlet',0,2,NOW(),NOW())
ON DUPLICATE KEY UPDATE `name`=VALUES(`name`),`is_staff`=VALUES(`is_staff`),`sort`=VALUES(`sort`);

INSERT INTO `route_permissions` (`pattern`,`roles`,`sort`,`created_at`,`updated_at`) VALUES
  ('rbac.*','["admin"]',0,NOW(),NOW()),
  ('athletes.destroy','["admin"]',1,NOW(),NOW()),
  ('athletes.index','["admin","coach"]',2,NOW(),NOW()),
  ('athletes.create','["admin","coach"]',3,NOW(),NOW()),
  ('athletes.store','["admin","coach"]',4,NOW(),NOW()),
  ('athletes.edit','["admin","coach"]',5,NOW(),NOW()),
  ('athletes.update','["admin","coach"]',6,NOW(),NOW()),
  ('equipment.*','["admin","coach"]',7,NOW(),NOW()),
  ('records.*','["admin","coach"]',8,NOW(),NOW()),
  ('h2h','["admin","coach"]',9,NOW(),NOW())
ON DUPLICATE KEY UPDATE `roles`=VALUES(`roles`),`sort`=VALUES(`sort`);

DELETE FROM `menus`;
INSERT INTO `menus` (`label`,`route`,`param`,`roles`,`sort`,`is_active`,`created_at`,`updated_at`) VALUES
  ('Dashboard','dashboard',NULL,'["admin","coach","athlete"]',0,1,NOW(),NOW()),
  ('Atlet','athletes.index',NULL,'["admin","coach"]',1,1,NOW(),NOW()),
  ('Head to Head','h2h',NULL,'["admin","coach"]',2,1,NOW(),NOW()),
  ('Akses (RBAC)','rbac.roles.index',NULL,'["admin"]',3,1,NOW(),NOW()),
  ('Profil Saya','athletes.show','self_athlete','["athlete"]',4,1,NOW(),NOW()),
  ('Buku Saku','shoot-log.edit','self_athlete','["athlete"]',5,1,NOW(),NOW());

COMMIT;

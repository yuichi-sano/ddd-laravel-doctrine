INSERT INTO sample.users
  (user_id, access_id, password, expires_at, created_at, created_user)
VALUES
  ('1', '1000', '$2a$10$ar/kbJOeloRw3NQPTW94IOtPvpW3PAElHn3a7FcvGU0qC7s6IMTMq', now() + interval '3 months', now(), 'system')
;


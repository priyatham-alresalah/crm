-- Seed users for Al Resalah – run once in Supabase SQL Editor.
-- Default password for all: 12345678
-- Creates auth.users + auth.identities (login) and public.users + public.profiles (CRM).
-- Requires: public.users and public.profiles tables exist. branch_id set to first active branch or NULL.
-- If you re-run and get duplicate key errors, remove existing users first (Auth dashboard or delete from auth.identities, auth.users, public.profiles, public.users for these emails).

create extension if not exists pgcrypto;

do $$
declare
  v_id uuid;
  v_pw text := crypt('12345678', gen_salt('bf'));
  v_instance_id uuid := coalesce((select id from auth.instances limit 1), '00000000-0000-0000-0000-000000000000'::uuid);
  v_branch_id uuid := (select id from public.branches where is_active = true limit 1);
  r record;
begin
  for r in
    select * from (values
      ('Jennifer Lenon', '971544484962', 'bdo@aresalah.com'),
      ('Ma. Catheryn Hipolito', '971502205731', 'contact@resala.ae'),
      ('Kamal krishnan', '971507312339', 'bd@aresalah.com'),
      ('Muhammed Aslam', '971509472700', 'training@resala.ae'),
      ('Ive De Venecia', '971505474392', 'resala@resala.ae'),
      ('Dhanesh Kumar Av', '971547871457', 'cr@aresalah.com'),
      ('Susha K Gopalan', '971544387323', 'admin@resala.ae'),
      ('Susan Thomas', '971502740096', 'accounts@aresalah.com'),
      ('Sapna Latha', '971505474561', 'bds@aresalah.com'),
      ('Sri Alamelu K V', '971509914110', 'hse.alresalah@gmail.com'),
      ('Abioye Waliu A', '2349130183535', 'contact@alresalahct.com')
    ) as t(full_name, phone, email)
  loop
    v_id := gen_random_uuid();

    insert into auth.users (
      id, instance_id, aud, role, email, encrypted_password,
      email_confirmed_at, raw_app_meta_data, raw_user_meta_data, created_at, updated_at
    ) values (
      v_id, v_instance_id, 'authenticated', 'authenticated', r.email, v_pw,
      now(), '{"provider":"email","providers":["email"]}'::jsonb,
      jsonb_build_object('full_name', r.full_name, 'phone', r.phone),
      now(), now()
    );

    insert into auth.identities (
      id, user_id, identity_data, provider, provider_id, last_sign_in_at, created_at, updated_at
    ) values (
      v_id, v_id,
      jsonb_build_object('sub', v_id::text, 'email', r.email),
      'email', v_id::text, now(), now(), now()
    );

    insert into public.users (id, name, role, is_active, branch_id)
    values (v_id, r.full_name, 'user', true, v_branch_id);

    insert into public.profiles (id, full_name, email, phone)
    values (v_id, r.full_name, r.email, r.phone);
  end loop;
end $$;

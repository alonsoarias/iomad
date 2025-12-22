-- Script para restaurar las posiciones desde el backup
-- Ejecutar en phpMyAdmin o MySQL CLI
-- Fecha: 2025-12-22

-- Vacantes con múltiples posiciones del backup
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-20';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-209';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-210';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-211';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-212';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-213';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-214';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-215';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-216';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-217';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-218';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-219';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-220';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-221';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-222';
UPDATE kjrt_local_jobboard_vacancy SET positions = 3 WHERE code = 'FCAS-223';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-224';
UPDATE kjrt_local_jobboard_vacancy SET positions = 3 WHERE code = 'FCAS-225';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-226';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-227';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-228';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-229';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-230';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'FCAS-231';
UPDATE kjrt_local_jobboard_vacancy SET positions = 3 WHERE code = 'BIEN-05';
UPDATE kjrt_local_jobboard_vacancy SET positions = 10 WHERE code = 'BIEN-06';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'BIEN-09';
UPDATE kjrt_local_jobboard_vacancy SET positions = 2 WHERE code = 'BIEN-11';
UPDATE kjrt_local_jobboard_vacancy SET positions = 15 WHERE code = 'BIEN-15';

-- Verificar el total de posiciones después de restaurar
SELECT SUM(positions) as total_positions, COUNT(*) as total_vacancies FROM kjrt_local_jobboard_vacancy;

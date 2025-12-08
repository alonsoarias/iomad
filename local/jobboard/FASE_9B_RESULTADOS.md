# Resultados Fase 9B: Extraccion de Texto

## Fecha: 2025-12-08

## Resumen Ejecutivo

Se extrajeron exitosamente los textos de los 33 fragmentos PDF creados en la Fase 9A.
Se identificaron **335 perfiles profesionales** distribuidos en dos facultades:
- FCAS (Ciencias Administrativas y Sociales): 294 perfiles
- FII (Ingenierias e Informatica): 41 perfiles

---

## Archivos de Texto Generados

| Archivo | Lineas | Caracteres | Estado |
|---------|--------|------------|--------|
| FCAS-PERFILES_PROFESORES_2026_p01.txt | 143 | 2889 | OK |
| FCAS-PERFILES_PROFESORES_2026_p02.txt | 160 | 3134 | OK |
| FCAS-PERFILES_PROFESORES_2026_p03.txt | 157 | 3329 | OK |
| FCAS-PERFILES_PROFESORES_2026_p04.txt | 140 | 3095 | OK |
| FCAS-PERFILES_PROFESORES_2026_p05.txt | 165 | 3425 | OK |
| FCAS-PERFILES_PROFESORES_2026_p06.txt | 140 | 2947 | OK |
| FCAS-PERFILES_PROFESORES_2026_p07.txt | 142 | 3075 | OK |
| FCAS-PERFILES_PROFESORES_2026_p08.txt | 153 | 3361 | OK |
| FCAS-PERFILES_PROFESORES_2026_p09.txt | 151 | 3599 | OK |
| FCAS-PERFILES_PROFESORES_2026_p10.txt | 148 | 3615 | OK |
| FCAS-PERFILES_PROFESORES_2026_p11.txt | 137 | 3032 | OK |
| FCAS-PERFILES_PROFESORES_2026_p12.txt | 150 | 3098 | OK |
| FCAS-PERFILES_PROFESORES_2026_p13.txt | 143 | 3275 | OK |
| FCAS-PERFILES_PROFESORES_2026_p14.txt | 156 | 3225 | OK |
| FCAS-PERFILES_PROFESORES_2026_p15.txt | 131 | 3009 | OK |
| FCAS-PERFILES_PROFESORES_2026_p16.txt | 152 | 3469 | OK |
| FCAS-PERFILES_PROFESORES_2026_p17.txt | 142 | 3314 | OK |
| FCAS-PERFILES_PROFESORES_2026_p18.txt | 137 | 3187 | OK |
| FCAS-PERFILES_PROFESORES_2026_p19.txt | 138 | 2926 | OK |
| FCAS-PERFILES_PROFESORES_2026_p20.txt | 149 | 3061 | OK |
| FCAS-PERFILES_PROFESORES_2026_p21.txt | 162 | 3258 | OK |
| FCAS-PERFILES_PROFESORES_2026_p22.txt | 138 | 3221 | OK |
| FCAS-PERFILES_PROFESORES_2026_p23.txt | 161 | 3194 | OK |
| FCAS-PERFILES_PROFESORES_2026_p24.txt | 135 | 2982 | OK |
| FCAS-PERFILES_PROFESORES_2026_p25.txt | 147 | 3117 | OK |
| FCAS-PERFILES_PROFESORES_2026_p26.txt | 11 | 127 | OK |
| PERFILES_PROFESORES_MODALIDAD_A_DISTANCIA_FII_2026_p01.txt | 155 | 2880 | OK |
| PERFILES_PROFESORES_MODALIDAD_A_DISTANCIA_FII_2026_p02.txt | 167 | 3299 | OK |
| PERFILES_PROFESORES_MODALIDAD_A_DISTANCIA_FII_2026_p03.txt | 155 | 3018 | OK |
| PERFILES_PROFESORES_MODALIDAD_A_DISTANCIA_FII_2026_p04.txt | 135 | 2570 | OK |
| PERFILES_PROFESORES_MODALIDAD_PRESENCIAL_FII_2026_p01.txt | 228 | 4111 | OK |
| PERFILES_PROFESORES_MODALIDAD_PRESENCIAL_FII_2026_p02.txt | 187 | 4120 | OK |
| PERFILES_PROFESORES_MODALIDAD_PRESENCIAL_FII_2026_p03.txt | 151 | 3159 | OK |

**Total archivos:** 33
**Total lineas:** 4,866
**Total caracteres:** 103,121
**Ubicacion:** `local/jobboard/PERFILESPROFESORES_TEXT/`

---

## Herramienta Utilizada

- **PyPDF2 3.0.1** (biblioteca Python para extraccion de texto de PDFs)
- Nota: `pdftotext` no disponible debido a restricciones de red

---

## Estructura de Perfiles Identificada

### Campos encontrados:

- [x] **CODIGO** - Identificador unico del perfil (ej. FCAS-01, FII-15)
- [x] **TIPO DE VINCULACION** - Tipo de contrato
  - OCASIONAL TIEMPO COMPLETO
  - CATEDRA
- [x] **PROGRAMA ACADEMICO** - Programa al que aplica el perfil
- [x] **PERFIL PROFESIONAL ESPECIFICO** - Formacion requerida
- [x] **POSIBLES CURSOS PARA ORIENTAR** - Lista de asignaturas

### Delimitador de perfiles:

Cada perfil esta delimitado por su **CODIGO** unico que sigue el patron:
- `FCAS-XXX` para Facultad de Ciencias Administrativas y Sociales
- `FII-XX` para Facultad de Ingenierias e Informatica

Los perfiles estan organizados en formato tabular dentro del PDF original,
con columnas para cada campo.

### Tipos de vinculacion identificados:

| Tipo | Cantidad |
|------|----------|
| CATEDRA | 423 menciones |
| OCASIONAL | 35 menciones |

---

## Modalidades Identificadas

- [x] **Presencial** - Campus Pamplona
- [x] **A Distancia** - Multiples sedes:
  - Tibu
  - Sardinata
  - Toledo
  - El Tarra

---

## Perfiles por Facultad

### FCAS - Facultad de Ciencias Administrativas y Sociales

**Total perfiles:** 294

**Programas academicos:**
- Tecnologia en Gestion Comunitaria
- Tecnologia en Gestion Empresarial
- (Otros programas de la facultad)

**Rango de codigos:** FCAS-01 a FCAS-139 (con algunas discontinuidades)

### FII - Facultad de Ingenierias e Informatica

**Total perfiles:** 41

**Programas academicos:**
- Tecnologia en Procesos Agroindustriales
- Tecnologia en Gestion de Redes y Sistemas Teleinformaticos
- Tecnologia en Proteccion y Recuperacion de Ecosistemas Forestales
- Tecnologia en Produccion Agropecuaria
- Tecnica Profesional en Procesos de Seguridad y Salud en el Trabajo

**Rango de codigos:** FII-01 a FII-41 (consecutivos)

---

## Muestra de Contenido

### Ejemplo de perfil completo (FCAS-01):

```
CODIGO: FCAS-01
TIPO DE VINCULACION: OCASIONAL TIEMPO COMPLETO
PROGRAMA ACADEMICO: TECNOLOGIA EN GESTION COMUNITARIA
PERFIL PROFESIONAL ESPECIFICO: PROFESIONAL EN TRABAJO SOCIAL
POSIBLES CURSOS PARA ORIENTAR:
  - SISTEMATIZACION DE EXPERIENCIAS
  - SUJETO Y FAMILIA
  - COORDINACION PROGRAMA ACADEMICO
  - DIRECCION DE TRABAJO DE GRADO
  - COMITE CURRICULAR DEL PROGRAMA
```

### Ejemplo de perfil FII (FII-01):

```
CODIGO: FII-01
TIPO DE VINCULACION: OCASIONAL TIEMPO COMPLETO
PROGRAMA ACADEMICO: TECNOLOGIA EN PROCESOS AGROINDUSTRIALES
PERFIL PROFESIONAL ESPECIFICO: INGENIERO DE ALIMENTOS / INGENIERO AGROINDUSTRIAL /
                                INGENIERO DE PRODUCCION AGROINDUSTRIAL O AREAS AFINES
POSIBLES CURSOS PARA ORIENTAR:
  - INTRODUCCION A LA AGROINDUSTRIA
  - PROCESOS AGROALIMENTARIOS I
  - PROCESOS AGROALIMENTARIOS II
```

---

## Archivo Consolidado

Se creo un archivo consolidado con todo el contenido extraido:

- **Archivo:** `local/jobboard/PERFILESPROFESORES_TEXT/_CONSOLIDADO.txt`
- **Tamano:** 109.3 KB
- **Lineas:** 4,970

Este archivo contiene todos los textos concatenados en orden, util para
busquedas globales y analisis completo.

---

## Verificaciones Realizadas

- [x] Todos los fragmentos PDF procesados (33/33)
- [x] Todos los archivos de texto generados correctamente
- [x] Contenido verificado - ningun archivo vacio
- [x] Estructura de perfiles identificada
- [x] Campos de cada perfil documentados
- [x] Modalidades identificadas
- [x] Archivo consolidado generado

---

## Estado

- [x] **COMPLETADO** - Listo para Fase 9C

---

## Notas para Fase 9C

1. El texto extraido mantiene el formato tabular original pero en texto plano
2. Algunos caracteres especiales pueden estar separados (ej. "PRESEN CIAL" en lugar de "PRESENCIAL")
3. Los codigos de perfil son consistentes y pueden usarse como identificadores unicos
4. Se recomienda usar expresiones regulares para parsear los campos
5. El archivo consolidado facilita busquedas globales

---

## Proximo Paso

Proceder a **FASE 9C** para parsear los textos extraidos y crear la estructura
JSON con todos los perfiles profesionales.

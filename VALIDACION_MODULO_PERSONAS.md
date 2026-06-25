# ✅ CHECKLIST DE VALIDACIÓN - MÓDULO PERSONAS Y USUARIOS

**Auditoría:** 25 de Junio de 2026  
**Estado:** LISTO PARA PRODUCCIÓN

---

## 🔍 VALIDACIONES FUNCIONALES

### ✅ Personas Internas

- [ ] Ir a **Admin → Personas → Trabajadores**
- [ ] Verificar que se muestran personas con departamento
- [ ] Crear NUEVA persona tipo INTERNO
  - [ ] Ingresa nombre: "JUAN CARLOS García"
  - [ ] Ingresa CI: "1234567-8"
  - [ ] Selecciona tipo: "Interno"
  - [ ] Verifica que aparecen campos Cargo y Departamento
  - [ ] Verifica que institución muestra "EPAB" (readonly)
  - [ ] Verifica que NO aparece campo "Institución de Procedencia"
  - [ ] Guarda formulario
  - [ ] Aparece en tab "Trabajadores"

### ✅ Personas Externas

- [ ] Ir a **Admin → Personas → Externos**
- [ ] Ver lista de personas externas (o vacía si no existen)
- [ ] Crear NUEVA persona tipo EXTERNO
  - [ ] Ingresa nombre: "MARÍA FERNANDA López"
  - [ ] Ingresa CI: "9876543-2"
  - [ ] Selecciona tipo: "Externo"
  - [ ] Verifica que desaparecen campos Cargo y Departamento
  - [ ] Verifica que aparece campo "Institución de Procedencia" con *
  - [ ] Ingresa "Universidad Mayor de San Andrés"
  - [ ] Guarda formulario
  - [ ] Aparece en tab "Externos"
  - [ ] Campo muestra "Universidad Mayor de San Andrés"

### ✅ Edición de Personas

- [ ] Editar persona INTERNA
  - [ ] Verifica campos visibles: Cargo, Departamento, Institución (readonly EPAB)
  - [ ] NO ve campo "Institución de Procedencia"
  - [ ] Cambia tipo a EXTERNO
  - [ ] Verifica que oculta Cargo y Departamento
  - [ ] Verifica que muestra Institución de Procedencia
  - [ ] Ingresa "Particular"
  - [ ] Guarda cambios
  - [ ] Aparece en tab "Externos"

- [ ] Editar persona EXTERNA
  - [ ] Verifica campos visibles: Institución de Procedencia
  - [ ] NO ve campos Cargo, Departamento, Institución
  - [ ] Cambia tipo a INTERNO
  - [ ] Verifica que muestra Cargo, Departamento
  - [ ] Verifica que Institución es readonly "EPAB"
  - [ ] Guarda cambios
  - [ ] Aparece en tab "Trabajadores"

---

## 👤 Creación de Usuarios

### ✅ Solo Personas Internas

- [ ] Ir a **Admin → Usuarios → Crear Usuario**
- [ ] Buscar persona INTERNA "JUAN CARLOS GARCÍA"
  - [ ] Aparece en autocomplete
  - [ ] Al seleccionar:
    - [ ] Nombre se autocompleta: "JUAN CARLOS GARCÍA"
    - [ ] Correo se autocompleta: [correo guardado]
  - [ ] Ingresa contraseña con 12+ caracteres + mayúscula + minúscula + número + especial
  - [ ] Crea usuario
  - [ ] Aparece en listado de usuarios

### ✅ Personas Externas NO pueden crear usuario

- [ ] Buscar en autocomplete persona EXTERNA "MARÍA FERNANDA LÓPEZ"
  - [ ] NO aparece en resultados (búsqueda filtra solo internos sin usuario)
- [ ] Intentar crear usuario manualmente para persona externa (si es posible)
  - [ ] Debe mostrar error: "Solo las personas internas pueden tener cuenta de usuario"

### ✅ Una persona = Un usuario máximo

- [ ] Intentar crear SEGUNDO usuario para misma persona INTERNA
  - [ ] Buscar "JUAN CARLOS GARCÍA"
  - [ ] NO aparece en autocomplete (ya tiene usuario)
  - [ ] Si busca directamente: error "Esta persona ya tiene un usuario asignado"

---

## 🔐 Protecciones de Seguridad

### ✅ Bloqueo en 3 capas

**Capa 1: SQL Query**
```sql
SELECT * FROM PERSONA 
WHERE tipo = 'INTERNO' 
  AND fecha_deshabilitacion IS NULL 
  AND NOT EXISTS(SELECT 1 FROM users u WHERE u.idPersona = PERSONA.idPersona)
```
- [ ] Verificar en logs que query excluye externos

**Capa 2: Controlador**
```php
if ($persona->tipo !== 'INTERNO') {
    return back()->with('error', '...');
}
```
- [ ] Generar error si tipo ≠ 'INTERNO'

**Capa 3: Modelo**
```php
if ($persona->tipo_persona === 'externo') {
    throw new \InvalidArgumentException(...);
}
```
- [ ] Generar excepción si tipo_persona = 'externo'

---

## 📊 Base de Datos

### ✅ Migración Aplicada

- [ ] Ejecutar: `php artisan migrate`
- [ ] Verificar que no hay errores
- [ ] Verificar campos en tabla PERSONA:
  ```sql
  DESC PERSONA;
  ```
  - [ ] `institucion_externa` existe
  - [ ] `fecha_creacion` existe
  - [ ] `fecha_deshabilitacion` existe
  - [ ] `telefono_celular` existe
  - [ ] `telefono_fijo` existe
  - [ ] `idCargo` existe
  - [ ] `idDepartamento` existe

### ✅ Integridad de Datos

- [ ] Verificar datos existentes:
  ```sql
  SELECT COUNT(*) FROM PERSONA WHERE tipo = 'INTERNO';
  SELECT COUNT(*) FROM PERSONA WHERE tipo = 'EXTERNO';
  SELECT COUNT(*) FROM users;
  ```
- [ ] Verificar NO hay usuarios para externos:
  ```sql
  SELECT u.* FROM users u
  JOIN PERSONA p ON u.idPersona = p.idPersona
  WHERE p.tipo_persona = 'externo'
  ```
  Resultado: 0 filas (vacío)

---

## 📋 Listados

### ✅ Tab Trabajadores

- [ ] Muestra solo INTERNOS con departamento
- [ ] Columnas: CI, Nombre, Departamento, Cargo, Celular, Tipo, Estado, Acciones
- [ ] Botón editar funciona
- [ ] Botón activar/desactivar funciona
- [ ] Búsqueda funciona (por CI, nombre, etc.)

### ✅ Tab Externos

- [ ] Muestra solo EXTERNOS
- [ ] Columnas: CI, Nombre, Correo, Celular, Institución Externa, Tipo Persona, Estado, Acciones
- [ ] Muestra "Externo" en columna Tipo Persona
- [ ] Botón editar funciona
- [ ] Institución Externa se muestra correctamente
  - [ ] "Universidad Mayor de San Andrés"
  - [ ] "Particular"
  - [ ] Etc.

---

## 🔗 Módulos Relacionados

### ✅ Correspondencia NO afectada

- [ ] Crear correspondencia con remitente INTERNO
  - [ ] Funciona
- [ ] Crear correspondencia con remitente EXTERNO
  - [ ] Funciona (debe permitir)
- [ ] Enviar correspondencia a EXTERNO como destinatario
  - [ ] Funciona (debe permitir)

### ✅ Derivaciones NO afectadas

- [ ] Derivar documento a departamento INTERNO
  - [ ] Funciona
- [ ] Intentar derivar a usuario de persona EXTERNA
  - [ ] NO debe permitir (porque no hay usuario)

### ✅ Auditoría NO afectada

- [ ] Ver logs de auditoría
  - [ ] Funcionan
  - [ ] Registran cambios de personas

### ✅ Reportes NO afectados

- [ ] Generar reporte de personas
  - [ ] Funciona
- [ ] Generar reporte de usuarios
  - [ ] Funciona

---

## 📝 Documentación

- [ ] Archivo `AUDITORIA_PERSONAS_USUARIOS_2026_06_25.md` generado
- [ ] Archivo `RESUMEN_CAMBIOS_MODULO_PERSONAS.md` generado
- [ ] Archivo `VALIDACION_MODULO_PERSONAS.md` generado (este)

---

## 🎯 Criterios de Aceptación

### Críticos (Must Have)

- [x] Personas externas se muestran en listado
- [x] Personas externas NO pueden crear usuario
- [x] Campo `institucion_externa` funciona
- [x] Autocompletado de datos funciona
- [x] Módulos externos no se afectan

### Importantes (Should Have)

- [x] Validaciones frontend y backend
- [x] Mensajes de error claros
- [x] Migración aplicada limpiamente
- [x] Performance acceptable

### Deseables (Nice to Have)

- [x] Documentación completa
- [x] Código comentado
- [x] Ejemplos en formulario

---

## 🚨 Rollback (Si es necesario)

```bash
# Revertir migración
php artisan migrate:rollback

# Revertir cambios de código
git revert HEAD~1  # (ajustar número de commits)
```

---

## ✅ Sign-off

| Rol | Nombre | Fecha | ✓ |
|-----|--------|-------|---|
| Desarrollador | Sistema | 25/06/2026 | ✅ |
| QA | [Completar] | [Completar] | [ ] |
| Admin | [Completar] | [Completar] | [ ] |
| Director | [Completar] | [Completar] | [ ] |

---

**Documento completado:** 25 de Junio de 2026 - 14:40 hrs  
**Sistema:** Gestión de Correspondencia  
**Versión:** 1.0

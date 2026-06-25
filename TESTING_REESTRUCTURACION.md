# Testing de Reestructuración - Módulos Correspondencia, Mi Bandeja, Envíos

## Instrucciones para Testing Manual

### Prerequisitos

1. Asegúrate de tener Laravel corriendo: `php artisan serve`
2. Ten dos navegadores o pestañas abiertas:
   - **Pestaña 1**: Acceso como **Admin** (idRol = 1)
   - **Pestaña 2**: Acceso como **Usuario Normal** (idRol ≠ 1)

---

## TESTING ADMIN (idRol = 1)

### Test 1: Módulo Correspondencia - Listado
**Ruta**: `/correspondencia`  
**Esperado**: Muestra vista `admin.correspondencia.index`  
**Verificar**:
- [ ] Carga la vista correcta (header/footer admin)
- [ ] Muestra TODOS los documentos (sin filtro por usuario)
- [ ] Puede buscar por CITE y asunto
- [ ] Puede filtrar por estado
- [ ] Puede filtrar por urgencia
- [ ] No hay errores en consola
- [ ] No hay excepción `RouteNotFoundException`

### Test 2: Módulo Correspondencia - Detalle
**Ruta**: `/correspondencia/{id}`  
**Esperado**: Muestra vista `admin.correspondencia.show`  
**Verificar**:
- [ ] Carga la vista correcta
- [ ] Muestra información completa del documento
- [ ] Muestra derivaciones
- [ ] Botón "Derivar" está disponible
- [ ] Botón "Finalizar" está disponible
- [ ] Puede hacer clic en "Derivar" sin errores

### Test 3: Módulo Envíos - Listado
**Ruta**: `/envios`  
**Esperado**: Muestra vista `admin.envios.index`  
**Verificar**:
- [ ] Carga la vista correcta
- [ ] Muestra TODOS los envíos (admin ve todos)
- [ ] Muestra estado "En tránsito" y "Recibidos"
- [ ] Puede filtrar por departamento
- [ ] Puede filtrar por estado
- [ ] No hay errores

### Test 4: Módulo Envíos - Mi Bandeja
**Ruta**: `/mi-bandeja`  
**Esperado**: Muestra vista `admin.bandeja.index`  
**Verificar**:
- [ ] Carga la vista correcta
- [ ] Muestra TODOS los documentos en bandeja
- [ ] Puede filtrar y buscar
- [ ] No hay errores

### Test 5: Módulo Envíos - Derivación
**Ruta**: `/envios/{id}/derivar`  
**Esperado**: Muestra vista `admin.envios.derivar`  
**Verificar**:
- [ ] Carga el formulario de derivación admin
- [ ] Puede seleccionar departamento destino
- [ ] Puede escribir instrucción
- [ ] Puede derivar documento sin errores

### Test 6: Bandeja por Estados - Pendientes
**Ruta**: `/bandeja/pendientes`  
**Esperado**: Muestra vista `admin.bandeja.pendientes`  
**Verificar**:
- [ ] Muestra TODOS los documentos con estado "Pendiente"
- [ ] Cuenta coincide con total esperado
- [ ] No filtra por usuario (admin ve todos)

### Test 7: Bandeja por Estados - Recibidos
**Ruta**: `/bandeja/recibidos`  
**Esperado**: Muestra vista `admin.bandeja.recibidos`  
**Verificar**:
- [ ] Muestra TODOS los documentos con estado "Recibido"

### Test 8: Bandeja por Estados - Atendidos
**Ruta**: `/bandeja/atendidos`  
**Esperado**: Muestra vista `admin.bandeja.atendidos`  
**Verificar**:
- [ ] Muestra TODOS los documentos con estado "Atendido"

### Test 9: Bandeja por Estados - Archivados
**Ruta**: `/bandeja/archivados`  
**Esperado**: Muestra vista `admin.bandeja.archivados`  
**Verificar**:
- [ ] Muestra TODOS los documentos con estado "Archivado"

---

## TESTING USUARIO NORMAL (idRol ≠ 1)

### Test 10: Módulo Correspondencia - Listado (User)
**Ruta**: `/correspondencia`  
**Esperado**: Muestra vista `user.correspondencia.index`  
**Verificar**:
- [ ] Carga la vista correcta (header/footer user)
- [ ] Muestra SOLO sus documentos (filtrado por `idUsuario`)
- [ ] **NO puede** ver documentos de otros usuarios
- [ ] Cantidad de documentos es menor o igual a la vista admin
- [ ] Puede buscar y filtrar (solo en sus documentos)

### Test 11: Módulo Correspondencia - Detalle (User)
**Ruta**: `/correspondencia/{id}`  
**Esperado**: Muestra vista `user.correspondencia.show`  
**Verificar**:
- [ ] Carga la vista correcta
- [ ] Muestra solo el documento suyo
- [ ] **NO puede** acceder a documento de otro usuario
  - Prueba: Intenta acceder manualmente a `/correspondencia/{id_de_otro}`
  - Esperado: Error 403 o redirección

### Test 12: Módulo Envíos - Listado (User)
**Ruta**: `/envios`  
**Esperado**: Muestra vista `user.envios.index`  
**Verificar**:
- [ ] Carga la vista correcta
- [ ] Muestra SOLO los envíos que él envió (filtrado por `idUsuarioEnvio`)
- [ ] No muestra envíos de otros usuarios
- [ ] Cantidad es menor que la vista admin

### Test 13: Módulo Envíos - Mi Bandeja (User)
**Ruta**: `/mi-bandeja`  
**Esperado**: Muestra vista `user.bandeja.index`  
**Verificar**:
- [ ] Carga la vista correcta
- [ ] Muestra SOLO sus documentos en bandeja
- [ ] No muestra documentos de otros usuarios

### Test 14: Módulo Envíos - Derivación (User)
**Ruta**: `/envios/{id}/derivar`  
**Esperado**: Muestra vista `user.envios.derivar`  
**Verificar**:
- [ ] Carga el formulario de derivación user (diferente al admin)
- [ ] Solo puede derivar su propio documento
- [ ] **NO puede** derivar documento de otro usuario

### Test 15: Bandeja por Estados - Pendientes (User)
**Ruta**: `/bandeja/pendientes`  
**Esperado**: Muestra vista `user.bandeja.pendientes`  
**Verificar**:
- [ ] Muestra SOLO sus documentos con estado "Pendiente"
- [ ] No muestra documentos de otros usuarios
- [ ] Cantidad es menor o igual a admin

### Test 16: Bandeja por Estados - Recibidos (User)
**Ruta**: `/bandeja/recibidos`  
**Verificar**:
- [ ] Muestra SOLO sus documentos Recibidos

### Test 17: Bandeja por Estados - Atendidos (User)
**Ruta**: `/bandeja/atendidos`  
**Verificar**:
- [ ] Muestra SOLO sus documentos Atendidos

### Test 18: Bandeja por Estados - Archivados (User)
**Ruta**: `/bandeja/archivados`  
**Verificar**:
- [ ] Muestra SOLO sus documentos Archivados

---

## TESTING SIDEBAR NAVIGATION

### Test 19: Sidebar - Admin
**Acceso**: Como Admin  
**Verificar**:
- [ ] Dashboard → Route works (`admin.dashboard`)
- [ ] Documentos → `/correspondencia` (muestra admin view)
- [ ] Mi Bandeja → `/mi-bandeja` (muestra admin view)
- [ ] Enviadas → `/envios` (muestra admin view)
- [ ] Sección "Administración" visible
  - [ ] Usuarios → `/admin/usuarios`
  - [ ] Departamentos → `/admin/departamentos`
  - [ ] Personas → `/admin/personas`
  - [ ] Gestión Documental → `/admin/documentos` ✅ (Verificar que sigue funcionando)
  - [ ] Reportes → `/admin/reportes`
  - [ ] Auditoría → `/admin/auditoria`

### Test 20: Sidebar - Usuario Normal
**Acceso**: Como Usuario Normal  
**Verificar**:
- [ ] Dashboard → Route works (`user.dashboard`)
- [ ] Documentos → `/correspondencia` (muestra user view)
- [ ] Mi Bandeja → `/mi-bandeja` (muestra user view)
- [ ] Enviadas → `/envios` (muestra user view)
- [ ] Sección "Administración" **NO visible**
- [ ] Configuración → `/user/configuracion` funciona

---

## TESTING COMPARATIVO (Admin vs User)

### Test 21: Comparación de Documentos
**Acción**:
1. Admin en `/correspondencia` → Contar documentos totales (ej: 50)
2. User en `/correspondencia` → Contar documentos (ej: 5)
3. **Verificar**: User < Admin (documentos filtrados correctamente)

### Test 22: Comparación de Envíos
**Acción**:
1. Admin en `/envios` → Ver todos los envíos (ej: 30)
2. User en `/envios` → Ver solo sus envíos (ej: 3)
3. **Verificar**: User ≤ Admin

### Test 23: Comparación de Estados en Bandeja
**Acción**:
1. Admin en `/bandeja/pendientes` → Contar (ej: 10 documentos)
2. User en `/bandeja/pendientes` → Contar (ej: 2 documentos)
3. **Verificar**: Diferencia es significativa (filtro funciona)

---

## TESTING DE MÓDULOS RELACIONADOS

### Test 24: Gestión Documental (Admin)
**Ruta**: `/admin/documentos`  
**Verificar**:
- [ ] Muestra listado de documentos
- [ ] Pueden editar documentos
- [ ] Pueden subir PDFs
- [ ] No hay cambios en esta funcionalidad ✅

### Test 25: Dashboard Admin
**Ruta**: `/admin/dashboard`  
**Verificar**:
- [ ] Carga correctamente
- [ ] Muestra estadísticas
- [ ] No hay errores
- [ ] Botones de acceso rápido funcionan

### Test 26: Dashboard User
**Ruta**: `/user/dashboard`  
**Verificar**:
- [ ] Carga correctamente
- [ ] Muestra solo información del usuario
- [ ] No hay errores

---

## TESTING DE SEGURIDAD

### Test 27: Acceso No Autorizado - Admin URL
**Acción**: Como Usuario Normal, intenta acceder a `/admin/correspondencia`  
**Esperado**:
- [ ] Error 403 (Forbidden) O
- [ ] Redirección a acceso denegado O
- [ ] Muestra vista sin información sensible

### Test 28: Acceso No Autorizado - Documento Ajeno
**Acción**: Como Usuario A, intenta acceder a `/correspondencia/{id_de_usuario_B}`  
**Esperado**:
- [ ] Error 403 O
- [ ] No muestra documento de otro usuario

### Test 29: Acceso No Autorizado - Derivar Documento Ajeno
**Acción**: Como Usuario A, intenta derivar `/envios/{id_de_usuario_B}/derivar`  
**Esperado**:
- [ ] Error 403 O
- [ ] No permite derivar documento ajeno

---

## TESTING DE ERRORES

### Test 30: Routes No Definidas
**Acción**: Busca en consola del navegador (F12 → Console)  
**Verificar**:
- [ ] No hay errores `RouteNotFoundException`
- [ ] No hay errores de vista no encontrada
- [ ] No hay 404 en recursos (CSS, JS, imágenes)

### Test 31: Vistas No Encontradas
**Acción**: Accede a cada ruta y verifica errores en logs  
```bash
# En terminal Laravel
php artisan tinker
# Luego acceder a rutas
```
**Verificar**:
- [ ] No hay errores `view not found`
- [ ] No hay excepciones en logs

---

## CHECKLIST FINAL

- [ ] Todos los tests de Admin pasaron
- [ ] Todos los tests de Usuario pasaron
- [ ] Sidebar funciona correctamente
- [ ] Gestión Documental sigue funcionando
- [ ] No hay errores 404
- [ ] No hay RouteNotFoundException
- [ ] No hay ViewNotFoundException
- [ ] Seguridad: Users no pueden ver documentos de otros
- [ ] Seguridad: Users no pueden acceder a URLs de admin
- [ ] Performance: Las consultas usan filtros correctos
- [ ] UI: Las vistas se cargan correctamente
- [ ] Validaciones: Los formularios validan correctamente

---

## Notas de Testing

1. **Usuarios de prueba necesarios**:
   - Admin: Email con idRol = 1
   - User 1: Email con idRol ≠ 1
   - User 2: Email con idRol ≠ 1

2. **Documentos de prueba necesarios**:
   - Correspondencia asignada a Admin
   - Correspondencia asignada a User 1
   - Correspondencia asignada a User 2
   - Correspondencia en diferentes estados (Pendiente, Recibido, Atendido, Archivado)

3. **Verificación de Base de Datos**:
   ```sql
   -- Verificar usuarios admin
   SELECT id, name, idRol FROM USUARIO WHERE idRol = 1;
   
   -- Verificar usuarios normales
   SELECT id, name, idRol FROM USUARIO WHERE idRol != 1;
   
   -- Verificar correspondencia
   SELECT idDocumento, cite, idUsuario FROM CORRESPONDENCIA LIMIT 10;
   ```

---

**Documento de Testing**: 24 de junio de 2026

package com.respositorio.tareas.gestor_tareas.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import com.respositorio.tareas.gestor_tareas.model.Tarea;

public interface TareaRepository extends JpaRepository<Tarea, Long> {
}
/*Factory Method (Spring utiliza el patrón Factory Method para devolver implementaciones dinámicas del repositorio sin que tú las implementes manualmente. */
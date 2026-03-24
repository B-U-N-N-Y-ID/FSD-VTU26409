package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;
import org.springframework.data.domain.*;

import java.util.List;

@RestController
@RequestMapping("/api")
public class StudentController {

    @Autowired
    private StudentRepository repo;

    // CREATE
    @PostMapping("/add")
    public Student add(@RequestBody Student s) {
        return repo.save(s);
    }

    // READ ALL
    @GetMapping("/students")
    public List<Student> getAll() {
        return repo.findAll();
    }

    // FILTER BY DEPARTMENT
    @GetMapping("/department/{dept}")
    public List<Student> byDept(@PathVariable String dept) {
        return repo.findByDepartment(dept);
    }

    // FILTER BY AGE
    @GetMapping("/age/{age}")
    public List<Student> byAge(@PathVariable int age) {
        return repo.findByAgeGreaterThan(age);
    }

    // SORTING
    @GetMapping("/sort")
    public List<Student> sort() {
        return repo.findAll(Sort.by("name").ascending());
    }

    // PAGINATION
    @GetMapping("/page")
    public List<Student> page() {
        return repo.findAll(PageRequest.of(0, 2)).getContent();
    }
}
package com.example.demo;

import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.bind.annotation.RequestMapping;

@RestController
public class EmployeeController {

    @RequestMapping("/employee")
    public String showEmployee() {
        return "Employee Details: ID=1, Name=Rahul";
    }
}
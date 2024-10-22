--------------------------------------------------------
--  File created - Monday-October-21-2024   
--------------------------------------------------------
--------------------------------------------------------
--  DDL for Package PKG_EMPLOYEE_MANAGEMENT
--------------------------------------------------------

  CREATE OR REPLACE EDITIONABLE PACKAGE "EMS"."PKG_EMPLOYEE_MANAGEMENT" AS
    PROCEDURE Proc_Add_employee(
        p_first_name IN VARCHAR2,
        p_last_name IN VARCHAR2,
        p_email IN VARCHAR2,
        p_phone_number IN VARCHAR2,
        p_hire_date IN DATE,
        p_job_id IN VARCHAR2,
        p_salary IN NUMBER,
        p_department_id IN NUMBER
    );

    PROCEDURE Proc_update_employee(
        p_employee_id IN NUMBER,
        p_first_name IN VARCHAR2,
        p_last_name IN VARCHAR2,
        p_email IN VARCHAR2,
        p_phone_number IN VARCHAR2,
        p_job_id IN VARCHAR2,
        p_salary IN NUMBER,
        p_department_id IN NUMBER
    );

    PROCEDURE Proc_status_update(
        p_employee_id IN NUMBER,
        p_status in varchar2
    );

PROCEDURE proc_increase_salary(
    p_department_id IN NUMBER,
     p_salary_threshold IN NUMBER,
     p_increase_percentage IN NUMBER
);

END Pkg_employee_management;

/

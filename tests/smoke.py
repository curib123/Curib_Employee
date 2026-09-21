#!/usr/bin/env python3
"""
tests/smoke.py | 2026-09-22
End-to-end smoke test for the public auth flow and protected Employee CRUD.
"""

import html
import http.cookiejar
import re
import sys
import urllib.error
import urllib.parse
import urllib.request

BASE = "http://127.0.0.1:8080"
EMAIL = "ci-smoke@example.com"

jar = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))


def get(path):
    response = opener.open(BASE + path, timeout=20)
    body = response.read().decode("utf-8", "replace")
    return response, body


def post(path, fields):
    payload = urllib.parse.urlencode(fields).encode()
    request = urllib.request.Request(BASE + path, data=payload, method="POST")
    response = opener.open(request, timeout=20)
    body = response.read().decode("utf-8", "replace")
    return response, body


def csrf(body):
    match = re.search(
        r'name="curib_csrf_token"\s+value="([^"]+)"',
        body,
        re.IGNORECASE,
    )
    if not match:
        raise AssertionError("CSRF token not found")
    return html.unescape(match.group(1))


def require(text, needle, label):
    if needle not in text:
        raise AssertionError(f"{label}: expected {needle!r}")


def expect_http_error(path, status):
    try:
        opener.open(BASE + path, timeout=20)
    except urllib.error.HTTPError as exc:
        body = exc.read().decode("utf-8", "replace")
        if exc.code != status:
            raise AssertionError(
                f"{path}: expected HTTP {status}, received {exc.code}"
            )
        return body
    raise AssertionError(f"{path}: expected HTTP {status}")


def main():
    _, login = get("/login")
    require(login, "Sign in", "login page")
    require(login, "bootstrap@5.3.3", "Bootstrap version")
    require(login, "integrity=", "Bootstrap SRI")

    _, css = get("/assets/css/app.css")
    require(css, "--curib-primary", "local CSS asset")

    _, register = get("/register")
    require(register, "Create your account", "register page")

    _, generated = post(
        "/register",
        {
            "curib_csrf_token": csrf(register),
            "firstname": "CI",
            "lastname": "Smoke",
            "birthday": "2000-01-01",
            "address": "123 Automated Test Street",
            "contactno": "+639171234567",
            "email": EMAIL,
        },
    )
    require(generated, "Registration complete", "registration result")

    match = re.search(
        r'id="generatedPasswordValue"[^>]*>\s*([^<]+?)\s*</div>',
        generated,
        re.IGNORECASE | re.DOTALL,
    )
    if not match:
        raise AssertionError("Generated password was not shown")
    password = html.unescape(match.group(1)).strip()
    if len(password) != 16:
        raise AssertionError("Generated password is not 16 characters")

    _, login = get("/login")
    require(login, EMAIL, "prefilled registration email")

    _, dashboard = post(
        "/login",
        {
            "curib_csrf_token": csrf(login),
            "email": EMAIL,
            "password": password,
        },
    )
    require(dashboard, "Dashboard", "dashboard after login")
    require(
        dashboard,
        'data-first-login-password-modal="1"',
        "first-login password prompt",
    )

    _, dashboard = post(
        "/password/skip",
        {"curib_csrf_token": csrf(dashboard)},
    )
    require(
        dashboard,
        'data-first-login-password-modal="0"',
        "first-login prompt cleared",
    )

    _, employees = get("/employees")
    require(employees, "Employee Directory", "employees page")

    _, employees = post(
        "/employees/store",
        {
            "curib_csrf_token": csrf(employees),
            "firstname": "Smoke",
            "lastname": "Tester",
            "birthday": "1999-06-15",
            "address": "456 Employee Test Avenue",
            "contactno": "+639181112222",
        },
    )
    require(employees, "Smoke", "created employee first name")
    require(employees, "Tester", "created employee last name")

    method_error = expect_http_error("/employees/delete/1", 405)
    require(method_error, "Method Not Allowed", "GET delete method guard")

    not_found = expect_http_error("/this-route-does-not-exist", 404)
    require(not_found, "404", "404 page")

    _, logged_out = post(
        "/logout",
        {"curib_csrf_token": csrf(employees)},
    )
    require(logged_out, "Sign in", "logout redirect")

    _, protected = get("/employees")
    require(protected, "Sign in", "protected route after logout")

    print("Curib Employee end-to-end smoke test: PASS")


if __name__ == "__main__":
    try:
        main()
    except Exception as exc:
        print(f"Curib Employee end-to-end smoke test: FAIL: {exc}", file=sys.stderr)
        raise
